@extends('layouts.app')

@section('title', 'Turnieje – TennisArchive')
@section('heading', 'Turnieje')
@section('subheading', 'Archiwum wszystkich turniejów tenisowych')

@section('header-actions')
    <a href="{{ route('turnieje.create') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium transition-colors">
        <span>＋</span> Nowy turniej
    </a>
@endsection

@section('content')

@if($turnieje->isEmpty())
    {{-- Pusty stan --}}
    <div class="flex flex-col items-center justify-center py-32 text-center">
        <div class="mb-4 flex justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor" class="w-16 h-16 text-slate-700">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
            </svg>
        </div>
        <h2 class="text-xl font-bold text-white mb-2">Brak turniejów</h2>
        <p class="text-slate-400 mb-6">Dodaj pierwszy turniej, aby zacząć budować archiwum.</p>
        <a href="{{ route('turnieje.create') }}"
           class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium transition-colors">
            Dodaj turniej
        </a>
    </div>
@else
    {{-- Siatka kart turniejów --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach($turnieje as $turniej)
            <a href="{{ route('turnieje.show', $turniej->ID_Turnieju) }}"
               class="group rounded-2xl overflow-hidden border border-slate-800 hover:border-slate-600
                      transition-all hover:-translate-y-1 hover:shadow-2xl hover:shadow-blue-900/20"
               style="background-color:#0f172a">

                {{-- Kolorowy nagłówek karty (kolor zależny od nawierzchni) --}}
                <div class="h-28 bg-gradient-to-br {{ $turniej->kolorNawierzchni() }} relative overflow-hidden">
                    {{-- Tło: wielkie logo tenisa --}}
                    <div class="absolute inset-0 flex items-center justify-center opacity-10 select-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.7" stroke="white" class="w-20 h-20">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                        </svg>
                    </div>
                    {{-- Badge nawierzchni --}}
                    <div class="absolute top-3 right-3">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                     bg-black/30 backdrop-blur-sm text-white border border-white/20">
                            {{ $turniej->Nawierzchnia }}
                        </span>
                    </div>
                    {{-- Liczba edycji --}}
                    <div class="absolute bottom-3 left-3">
                        <span class="text-xs text-white/70">
                            {{ $turniej->edycje_count }}
                            {{ $turniej->edycje_count === 1 ? 'edycja' : ($turniej->edycje_count < 5 ? 'edycje' : 'edycji') }}
                        </span>
                    </div>
                </div>

                {{-- Treść karty --}}
                <div class="p-4">
                    <h3 class="font-bold text-white text-base leading-tight mb-1 group-hover:text-blue-400 transition-colors">
                        {{ $turniej->Nazwa }}
                    </h3>
                    <p class="text-slate-400 text-sm flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5 flex-shrink-0 opacity-60"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 0 0 .723 0l.028-.015.071-.041a16.975 16.975 0 0 0 1.144-.742 19.58 19.58 0 0 0 2.683-2.282c1.944-2.003 3.5-4.697 3.5-8.327a8 8 0 0 0-16 0c0 3.63 1.556 6.326 3.5 8.327a19.58 19.58 0 0 0 2.682 2.282 16.975 16.975 0 0 0 1.145.742ZM12 13.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/></svg>
                        {{ $turniej->Miasto }}
                        @if($turniej->kraj)
                            · {{ $turniej->kraj->Nazwa }}
                        @endif
                    </p>
                </div>
            </a>
        @endforeach
    </div>
@endif

@endsection
