@extends('layouts.app')

@php
    $stA = $mecz->stronaA;
    $stB = $mecz->stronaB;
    $winner = $mecz->zwycieska_strona;
    $nazwaA = $stA->map(fn($u) => $u->zawodnik->pelne_nazwisko)->join(' / ');
    $nazwaB = $stB->map(fn($u) => $u->zawodnik->pelne_nazwisko)->join(' / ');
@endphp

@section('title', ($nazwaA ?: 'Strona A') . ' vs ' . ($nazwaB ?: 'Strona B') . ' – TennisArchive')
@section('heading', 'Mecz')
@section('subheading', $mecz->edycja->turniej->Nazwa . ' ' . $mecz->edycja->Rok . ' · ' . $mecz->Runda)

@section('header-actions')
    <a href="{{ route('edycje.show', $mecz->edycja->ID_Edycji) }}"
       class="px-4 py-2 rounded-xl border border-slate-700 text-slate-400 hover:text-white text-sm transition-colors">
        ← Edycja
    </a>
@endsection

@section('content')

{{-- Główna karta meczu --}}
<div class="rounded-2xl border border-slate-800 overflow-hidden max-w-2xl mb-6"
     style="background-color:#0f172a">

    {{-- Nagłówek karty --}}
    <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-4 flex items-center gap-3 border-b border-slate-800">
        <span class="text-xs font-bold uppercase tracking-widest text-slate-400">
            {{ $mecz->Runda }}
        </span>
        @if($mecz->rodzaj)
            <span class="text-xs px-2 py-0.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-300">
                {{ $mecz->rodzaj->Nazwa }}
            </span>
        @endif
        <div class="flex-1"></div>
        <span class="text-xs text-slate-500 flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 flex-shrink-0"><path fill-rule="evenodd" d="M3 3h7.5v7.5H3V3Zm9.75 0h7.5v7.5h-7.5V3ZM3 12.75h7.5v7.5H3v-7.5Zm9.75 0h7.5v7.5h-7.5v-7.5Z" clip-rule="evenodd"/></svg>
            {{ $mecz->Kort }} · {{ $mecz->Data->format('d.m.Y') }}
        </span>
    </div>

    {{-- Vs: Strona A vs Strona B --}}
    <div class="p-6">
        <div class="grid grid-cols-[1fr,auto,1fr] gap-4 items-center">

            {{-- Strona A --}}
            <div class="text-right">
                @foreach($stA as $udzial)
                    <p class="font-bold {{ $winner === 'A' ? 'text-white text-lg' : 'text-slate-400' }} leading-tight">
                        {{ $udzial->zawodnik->pelne_nazwisko }}
                    </p>
                    <p class="text-xs text-slate-500">{{ $udzial->zawodnik->kraj->Kod_ISO ?? '' }}</p>
                @endforeach
                @if($winner === 'A')
                    <span class="inline-block mt-2 text-xs px-3 py-1 rounded-full
                                 bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-semibold">
                        ✓ Zwycięzca
                    </span>
                @endif
            </div>

            {{-- VS --}}
            <div class="flex flex-col items-center gap-1 px-2">
                <span class="text-xs font-black text-slate-600 tracking-widest">VS</span>
            </div>

            {{-- Strona B --}}
            <div class="text-left">
                @foreach($stB as $udzial)
                    <p class="font-bold {{ $winner === 'B' ? 'text-white text-lg' : 'text-slate-400' }} leading-tight">
                        {{ $udzial->zawodnik->pelne_nazwisko }}
                    </p>
                    <p class="text-xs text-slate-500">{{ $udzial->zawodnik->kraj->Kod_ISO ?? '' }}</p>
                @endforeach
                @if($winner === 'B')
                    <span class="inline-block mt-2 text-xs px-3 py-1 rounded-full
                                 bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-semibold">
                        ✓ Zwycięzca
                    </span>
                @endif
            </div>
        </div>

        {{-- Wyniki setów --}}
        @if($mecz->sety->isNotEmpty())
            <div class="mt-6 pt-5 border-t border-slate-800">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 mb-4">Wyniki setów</p>
                <div class="flex gap-3 justify-center flex-wrap">
                    @foreach($mecz->sety as $set)
                        @php
                            $wygraA = $set->Wynik_A !== null && $set->Wynik_B !== null && $set->Wynik_A > $set->Wynik_B;
                            $wygraB = $set->Wynik_A !== null && $set->Wynik_B !== null && $set->Wynik_B > $set->Wynik_A;
                        @endphp
                        <div class="flex flex-col items-center rounded-xl border border-slate-700 px-4 py-3 min-w-[60px]"
                             style="background-color:#1e293b">
                            <span class="text-xs text-slate-500 mb-1">S{{ $set->Numer_setu }}</span>
                            <span class="text-xl font-black {{ $wygraA ? 'text-white' : 'text-slate-500' }}">
                                {{ $set->Wynik_A ?? '–' }}
                            </span>
                            <span class="text-xl font-black {{ $wygraB ? 'text-white' : 'text-slate-500' }}">
                                {{ $set->Wynik_B ?? '–' }}
                            </span>
                            @if($set->Tie_break)
                                <span class="text-xs text-yellow-500 mt-1">TB</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Wynik skrócony --}}
                <p class="text-center mt-4 text-slate-400 text-sm">
                    <strong class="text-white">{{ $nazwaA ?: '?' }}</strong>
                    <span class="mx-2">
                        {{ $mecz->sety->map(fn($s) => $s->Wynik_A . '–' . $s->Wynik_B)->join('  ') }}
                    </span>
                    <strong class="text-white">{{ $nazwaB ?: '?' }}</strong>
                </p>
            </div>
        @endif

        {{-- Czas trwania --}}
        @if($mecz->Czas_trwania_min)
            <div class="mt-4 flex justify-center">
                <span class="text-xs text-slate-500 px-3 py-1 rounded-full bg-slate-800 border border-slate-700 flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" clip-rule="evenodd"/></svg>
                    {{ floor($mecz->Czas_trwania_min / 60) }}h {{ $mecz->Czas_trwania_min % 60 }}min
                </span>
            </div>
        @endif
    </div>
</div>

@endsection
