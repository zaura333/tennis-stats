@extends('layouts.app')

@section('title', 'Zawodnicy – TennisArchive')
@section('heading', 'Zawodnicy')
@section('subheading', 'Baza zawodników tenisowych')

@section('header-actions')
    <a href="{{ route('zawodnicy.create') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium transition-colors">
        <span>＋</span> Nowy zawodnik
    </a>
@endsection

@section('content')

{{-- Filtry i wyszukiwarka --}}
<form method="GET" action="{{ route('zawodnicy.index') }}"
      class="flex flex-wrap items-center gap-3 mb-6">

    <input type="text" name="szukaj" value="{{ request('szukaj') }}"
           placeholder="Szukaj po nazwisku lub imieniu…"
           class="flex-1 min-w-48 px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                  text-white placeholder-slate-500 focus:outline-none focus:border-blue-500
                  focus:ring-1 focus:ring-blue-500 text-sm">

    <select name="plec"
            class="px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                   text-white focus:outline-none focus:border-blue-500 text-sm">
        <option value="">Każda płeć</option>
        <option value="Mężczyzna" {{ request('plec') === 'Mężczyzna' ? 'selected' : '' }}>Mężczyźni</option>
        <option value="Kobieta"   {{ request('plec') === 'Kobieta'   ? 'selected' : '' }}>Kobiety</option>
        <option value="Inna"      {{ request('plec') === 'Inna'      ? 'selected' : '' }}>Inna</option>
    </select>

    <button type="submit"
            class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium transition-colors">
        Szukaj
    </button>

    @if(request()->hasAny(['szukaj', 'plec']))
        <a href="{{ route('zawodnicy.index') }}"
           class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-400 hover:text-white text-sm transition-colors">
            Wyczyść
        </a>
    @endif
</form>

@if($zawodnicy->isEmpty())
    <div class="text-center py-24">
        <div class="mb-3 flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor" class="w-14 h-14 text-slate-700">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
        </div>
        <h2 class="text-lg font-bold text-white mb-1">Brak zawodników</h2>
        <p class="text-slate-400 text-sm mb-5">Dodaj pierwszego zawodnika do bazy.</p>
        <a href="{{ route('zawodnicy.create') }}"
           class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium transition-colors">
            Dodaj zawodnika
        </a>
    </div>
@else
    {{-- Siatka kart zawodników --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($zawodnicy as $zawodnik)
            <a href="{{ route('zawodnicy.show', $zawodnik->Nr_Licencji_ITF) }}"
               class="group block rounded-2xl border border-slate-800 hover:border-slate-600
                      transition-all p-5"
               style="background-color:#0f172a">

                {{-- Avatar z inicjałami --}}
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-blue-800
                            flex items-center justify-center text-lg font-bold text-white mb-4">
                    {{ strtoupper(substr($zawodnik->Imie, 0, 1) . substr($zawodnik->Nazwisko, 0, 1)) }}
                </div>

                {{-- Dane --}}
                <h3 class="font-bold text-white text-base leading-tight mb-1 group-hover:text-blue-400 transition-colors">
                    {{ $zawodnik->pelne_nazwisko }}
                </h3>
                <p class="text-slate-400 text-xs mb-3">
                    {{ $zawodnik->kraj->Nazwa ?? $zawodnik->Kod_ISO }}
                    · ur. {{ $zawodnik->Data_urodzenia->format('Y') }}
                </p>

                {{-- Badges --}}
                <div class="flex flex-wrap gap-1.5">
                    <span class="text-xs px-2 py-0.5 rounded-full border
                                 {{ $zawodnik->Plec === 'Mężczyzna' ? 'bg-blue-500/10 border-blue-500/30 text-blue-300'
                                    : ($zawodnik->Plec === 'Kobieta' ? 'bg-pink-500/10 border-pink-500/30 text-pink-300'
                                    : 'bg-slate-700 border-slate-600 text-slate-300') }}">
                        {{ $zawodnik->Plec }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-slate-700/50 border border-slate-600/50 text-slate-400">
                        {{ $zawodnik->udzialy->count() }} turniej{{ $zawodnik->udzialy->count() === 1 ? '' : ($zawodnik->udzialy->count() < 5 ? 'e' : 'ów') }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Paginacja --}}
    <div class="mt-6">
        {{ $zawodnicy->withQueryString()->links('vendor.pagination.tailwind') }}
    </div>
@endif

@endsection
