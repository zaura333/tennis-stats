@extends('layouts.app')

@section('title', $edycja->turniej->Nazwa . ' ' . $edycja->Rok . ' – TennisArchive')
@section('heading', $edycja->turniej->Nazwa . ' ' . $edycja->Rok)
@section('subheading')
<span class="flex items-center gap-1.5">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5 opacity-60"><path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z" clip-rule="evenodd"/></svg>
    {{ $edycja->Data_rozpoczecia->format('d.m.Y') }} – {{ $edycja->Data_zakonczenia->format('d.m.Y') }}
</span>
@endsection

@section('header-actions')
    <a href="{{ route('mecze.create', $edycja->ID_Edycji) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium transition-colors">
        <span>＋</span> Dodaj mecz
    </a>
    <a href="{{ route('turnieje.show', $edycja->turniej->ID_Turnieju) }}"
       class="px-4 py-2 rounded-xl border border-slate-700 text-slate-400 hover:text-white text-sm transition-colors">
        ← Turniej
    </a>
@endsection

@section('content')

{{-- Dwie kolumny: mecze + zawodnicy --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Lewa kolumna: Mecze według rund --}}
    <div class="xl:col-span-2 space-y-6">
        <h2 class="text-base font-semibold text-slate-300">Drabinka / Mecze</h2>

        @if($edycja->mecze->isEmpty())
            <div class="rounded-2xl border border-slate-800 p-10 text-center" style="background-color:#0f172a">
                <p class="text-slate-500 text-sm">Brak meczów. Dodaj pierwszy mecz.</p>
                <a href="{{ route('mecze.create', $edycja->ID_Edycji) }}"
                   class="inline-block mt-3 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm transition-colors">
                    Dodaj mecz
                </a>
            </div>
        @else
            @foreach($kolejnoscRund as $runda)
                @if(isset($meczePoRundach[$runda]))
                    <div>
                        {{-- Nagłówek rundy --}}
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-xs font-bold uppercase tracking-widest text-slate-500">
                                {{ $runda }}
                            </span>
                            <div class="flex-1 h-px bg-slate-800"></div>
                        </div>

                        <div class="space-y-2">
                            @foreach($meczePoRundach[$runda] as $mecz)
                                @php
                                    // Zbieramy zawodników stron A i B
                                    $stA = $mecz->stronaA;
                                    $stB = $mecz->stronaB;
                                    $winner = $mecz->zwycieska_strona;
                                @endphp
                                <a href="{{ route('mecze.show', $mecz->ID_Meczu) }}"
                                   class="group block rounded-xl border border-slate-800 hover:border-slate-600
                                          transition-all p-4"
                                   style="background-color:#0f172a">

                                    <div class="flex items-center justify-between gap-4">
                                        {{-- Zawodnicy --}}
                                        <div class="flex-1 min-w-0">
                                            {{-- Strona A --}}
                                            <div class="flex items-center gap-2 mb-1.5">
                                                @if($winner === 'A')
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0"></span>
                                                @else
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-700 flex-shrink-0"></span>
                                                @endif
                                                <span class="text-sm font-medium truncate
                                                             {{ $winner === 'A' ? 'text-white' : 'text-slate-400' }}">
                                                    {{ $stA->map(fn($u) => $u->zawodnik->pelne_nazwisko)->join(' / ') ?: '—' }}
                                                </span>
                                            </div>
                                            {{-- Strona B --}}
                                            <div class="flex items-center gap-2">
                                                @if($winner === 'B')
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0"></span>
                                                @else
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-700 flex-shrink-0"></span>
                                                @endif
                                                <span class="text-sm font-medium truncate
                                                             {{ $winner === 'B' ? 'text-white' : 'text-slate-400' }}">
                                                    {{ $stB->map(fn($u) => $u->zawodnik->pelne_nazwisko)->join(' / ') ?: '—' }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Wyniki setów --}}
                                        <div class="flex items-center gap-1 flex-shrink-0">
                                            @foreach($mecz->sety as $set)
                                                <div class="text-center min-w-[2rem]">
                                                    <div class="text-xs font-bold {{ $winner === 'A' && $set->Wynik_A > $set->Wynik_B ? 'text-white' : 'text-slate-400' }}">
                                                        {{ $set->Wynik_A ?? '–' }}
                                                    </div>
                                                    <div class="text-xs font-bold {{ $winner === 'B' && $set->Wynik_B > $set->Wynik_A ? 'text-white' : 'text-slate-400' }}">
                                                        {{ $set->Wynik_B ?? '–' }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Badge rodzaju meczu --}}
                                        <span class="text-xs text-slate-500 flex-shrink-0">
                                            {{ $mecz->rodzaj->Nazwa ?? '' }}
                                        </span>
                                    </div>

                                    {{-- Kort i data --}}
                                    <p class="text-xs text-slate-600 mt-2 flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 flex-shrink-0"><path fill-rule="evenodd" d="M3 3h7.5v7.5H3V3Zm9.75 0h7.5v7.5h-7.5V3ZM3 12.75h7.5v7.5H3v-7.5Zm9.75 0h7.5v7.5h-7.5v-7.5Z" clip-rule="evenodd"/></svg>
                                        {{ $mecz->Kort }} · {{ $mecz->Data->format('d.m.Y') }}
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>

    {{-- Prawa kolumna: Lista zawodników --}}
    <div>
        <h2 class="text-base font-semibold text-slate-300 mb-4">
            Zawodnicy
            <span class="text-slate-600 font-normal text-sm">({{ $edycja->udzialy->count() }})</span>
        </h2>

        @if($edycja->udzialy->isEmpty())
            <div class="rounded-2xl border border-slate-800 p-6 text-center" style="background-color:#0f172a">
                <p class="text-slate-500 text-sm">Brak zawodników w tej edycji.</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($edycja->udzialy as $udzial)
                    <a href="{{ route('zawodnicy.show', $udzial->zawodnik->Nr_Licencji_ITF) }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-800
                              hover:border-slate-600 transition-all"
                       style="background-color:#0f172a">

                        {{-- Numer rozstawienia --}}
                        <span class="text-sm font-bold text-slate-500 w-5 text-right flex-shrink-0">
                            {{ $udzial->Rozstawienie > 0 ? $udzial->Rozstawienie : '–' }}
                        </span>

                        {{-- Avatar z inicjałami --}}
                        <div class="w-8 h-8 rounded-full bg-blue-600/20 border border-blue-600/30
                                    flex items-center justify-center text-xs font-bold text-blue-400 flex-shrink-0">
                            {{ strtoupper(substr($udzial->zawodnik->Imie, 0, 1) . substr($udzial->zawodnik->Nazwisko, 0, 1)) }}
                        </div>

                        {{-- Dane zawodnika --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">
                                {{ $udzial->zawodnik->pelne_nazwisko }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $udzial->zawodnik->kraj->Kod_ISO ?? '' }}
                                · {{ $udzial->Status }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

</div>

@endsection
