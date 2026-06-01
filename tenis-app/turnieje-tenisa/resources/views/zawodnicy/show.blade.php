@extends('layouts.app')

@section('title', $zawodnik->pelne_nazwisko . ' – TennisArchive')
@section('heading', $zawodnik->pelne_nazwisko)
@section('subheading', ($zawodnik->kraj->Nazwa ?? $zawodnik->Kod_ISO) . ' · ' . $zawodnik->Plec)

@section('header-actions')
    <a href="{{ route('zawodnicy.index') }}"
       class="px-4 py-2 rounded-xl border border-slate-700 text-slate-400 hover:text-white text-sm transition-colors">
        ← Wszyscy zawodnicy
    </a>
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Lewa: Karta profilu --}}
    <div>
        <div class="rounded-2xl border border-slate-800 p-6" style="background-color:#0f172a">
            {{-- Duży avatar --}}
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-800
                        flex items-center justify-center text-3xl font-black text-white mx-auto mb-5">
                {{ strtoupper(substr($zawodnik->Imie, 0, 1) . substr($zawodnik->Nazwisko, 0, 1)) }}
            </div>

            {{-- Imię i nazwisko --}}
            <h2 class="text-xl font-black text-white text-center mb-1">
                {{ $zawodnik->pelne_nazwisko }}
            </h2>
            <p class="text-slate-400 text-sm text-center mb-5">
                {{ $zawodnik->kraj->Nazwa ?? '' }}
            </p>

            {{-- Badge płci --}}
            <div class="flex justify-center mb-5">
                <span class="text-sm px-4 py-1 rounded-full border
                             {{ $zawodnik->Plec === 'Mężczyzna' ? 'bg-blue-500/10 border-blue-500/30 text-blue-300'
                                : ($zawodnik->Plec === 'Kobieta' ? 'bg-pink-500/10 border-pink-500/30 text-pink-300'
                                : 'bg-slate-700 border-slate-600 text-slate-300') }}">
                    {{ $zawodnik->Plec }}
                </span>
            </div>

            {{-- Dane szczegółowe --}}
            <div class="space-y-3 border-t border-slate-800 pt-4">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Nr licencji ITF</span>
                    <span class="text-white font-mono text-xs">{{ $zawodnik->Nr_Licencji_ITF }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Data urodzenia</span>
                    <span class="text-white">{{ $zawodnik->Data_urodzenia->format('d.m.Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Wiek</span>
                    <span class="text-white">{{ $zawodnik->wiek }} lat</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Kraj</span>
                    <span class="text-white">{{ $zawodnik->Kod_ISO }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Turnieje</span>
                    <span class="text-white font-bold">{{ $udzialy->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Prawa: Historia turniejów --}}
    <div class="lg:col-span-2">
        <h2 class="text-base font-semibold text-slate-300 mb-4">Historia turniejów</h2>

        @if($udzialy->isEmpty())
            <div class="rounded-2xl border border-slate-800 p-10 text-center" style="background-color:#0f172a">
                <p class="text-slate-500 text-sm">Zawodnik nie brał jeszcze udziału w żadnym turnieju.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($udzialy as $udzial)
                    <a href="{{ route('edycje.show', $udzial->edycja->ID_Edycji) }}"
                       class="flex items-center gap-4 rounded-xl border border-slate-800 hover:border-slate-600
                              transition-all p-4"
                       style="background-color:#0f172a">

                        {{-- Rok --}}
                        <span class="text-2xl font-black text-slate-600 w-16 text-center flex-shrink-0">
                            {{ $udzial->edycja->Rok }}
                        </span>

                        {{-- Kolor nawierzchni --}}
                        <div class="w-1 h-10 rounded-full flex-shrink-0
                                    bg-gradient-to-b {{ $udzial->edycja->turniej->kolorNawierzchni() }}">
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-semibold truncate">
                                {{ $udzial->edycja->turniej->Nazwa }}
                            </p>
                            <p class="text-slate-400 text-xs">
                                {{ $udzial->edycja->turniej->Miasto }}
                                · {{ $udzial->edycja->turniej->Nawierzchnia }}
                            </p>
                        </div>

                        {{-- Rozstawienie i status --}}
                        <div class="text-right flex-shrink-0">
                            @if($udzial->Rozstawienie > 0)
                                <p class="text-xs text-slate-400">Rozst. <strong class="text-white">{{ $udzial->Rozstawienie }}</strong></p>
                            @endif
                            <span class="text-xs px-2 py-0.5 rounded-full bg-slate-700 text-slate-300">
                                {{ $udzial->Status }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
