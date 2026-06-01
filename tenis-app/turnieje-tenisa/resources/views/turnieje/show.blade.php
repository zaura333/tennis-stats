@extends('layouts.app')

@section('title', $turniej->Nazwa . ' – TennisArchive')
@section('heading', $turniej->Nazwa)
@section('subheading')
<span class="flex items-center gap-1.5">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5 opacity-60"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 0 0 .723 0l.028-.015.071-.041a16.975 16.975 0 0 0 1.144-.742 19.58 19.58 0 0 0 2.683-2.282c1.944-2.003 3.5-4.697 3.5-8.327a8 8 0 0 0-16 0c0 3.63 1.556 6.326 3.5 8.327a19.58 19.58 0 0 0 2.682 2.282 16.975 16.975 0 0 0 1.145.742ZM12 13.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/></svg>
    {{ $turniej->Miasto }}{{ $turniej->kraj ? ' · ' . $turniej->kraj->Nazwa : '' }}
</span>
@endsection

@section('header-actions')
    <a href="{{ route('edycje.create', $turniej->ID_Turnieju) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium transition-colors">
        <span>＋</span> Nowa edycja
    </a>
@endsection

@section('content')

{{-- Informacje o turnieju --}}
<div class="rounded-2xl border border-slate-800 p-6 mb-8 flex flex-wrap items-center gap-6"
     style="background-color:#0f172a">

    {{-- Kolorowy blok nawierzchni --}}
    <div class="w-20 h-20 rounded-xl bg-gradient-to-br {{ $turniej->kolorNawierzchni() }}
                flex items-center justify-center flex-shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="white" class="w-10 h-10 opacity-90">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
        </svg>
    </div>

    <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2 mb-2">
            {{-- Badge nawierzchni --}}
            <span class="text-xs font-semibold px-3 py-1 rounded-full border {{ $turniej->badgeNawierzchni() }}">
                {{ $turniej->Nawierzchnia }}
            </span>
            {{-- Kraj --}}
            @if($turniej->kraj)
                <span class="text-xs font-medium px-3 py-1 rounded-full bg-slate-700/50 border border-slate-600/50 text-slate-300">
                    {{ $turniej->kraj->Kontynent }}
                </span>
            @endif
        </div>
        <p class="text-slate-400 text-sm">
            Łącznie <strong class="text-white">{{ $turniej->edycje->count() }}</strong>
            {{ $turniej->edycje->count() === 1 ? 'edycja' : ($turniej->edycje->count() < 5 ? 'edycje' : 'edycji') }}
            w archiwum
        </p>
    </div>

    <a href="{{ route('turnieje.index') }}"
       class="text-slate-400 hover:text-white text-sm transition-colors">
        ← Wróć do listy
    </a>
</div>

{{-- Lista edycji --}}
@if($turniej->edycje->isEmpty())
    <div class="text-center py-20">
        <div class="mb-3 flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor" class="w-14 h-14 text-slate-700">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v1.5M17.25 3v1.5M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
        </div>
        <h2 class="text-lg font-bold text-white mb-1">Brak edycji</h2>
        <p class="text-slate-400 text-sm mb-5">Dodaj pierwszą edycję roczną tego turnieju.</p>
        <a href="{{ route('edycje.create', $turniej->ID_Turnieju) }}"
           class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium transition-colors">
            Dodaj edycję
        </a>
    </div>
@else
    <h2 class="text-base font-semibold text-slate-300 mb-4">Edycje roczne</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($turniej->edycje as $edycja)
            <a href="{{ route('edycje.show', $edycja->ID_Edycji) }}"
               class="group block rounded-2xl border border-slate-800 hover:border-blue-600/50
                      transition-all p-5"
               style="background-color:#0f172a">

                {{-- Rok --}}
                <div class="flex items-center justify-between mb-3">
                    <span class="text-3xl font-black text-white group-hover:text-blue-400 transition-colors">
                        {{ $edycja->Rok }}
                    </span>
                    <span class="text-xs text-slate-500">→</span>
                </div>

                {{-- Daty --}}
                <p class="text-slate-400 text-xs mb-3 flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5 flex-shrink-0 opacity-60"><path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z" clip-rule="evenodd"/></svg>
                    {{ $edycja->Data_rozpoczecia->format('d.m.Y') }}
                    – {{ $edycja->Data_zakonczenia->format('d.m.Y') }}
                </p>

                {{-- Statystyki --}}
                <div class="flex gap-4 text-xs">
                    <span class="text-slate-400">
                        <strong class="text-white">{{ $edycja->udzialy->count() }}</strong>
                        zawodników
                    </span>
                    <span class="text-slate-400">
                        <strong class="text-white">{{ $edycja->mecze->count() }}</strong>
                        meczów
                    </span>
                </div>
            </a>
        @endforeach
    </div>
@endif

@endsection
