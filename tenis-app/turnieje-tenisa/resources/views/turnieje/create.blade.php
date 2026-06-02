@extends('layouts.app')

@section('title', 'Nowy turniej – TennisArchive')
@section('heading', 'Nowy turniej')
@section('subheading', 'Wypełnij dane stałe turnieju')

@section('content')

<div class="max-w-xl">
    <div class="rounded-2xl border border-slate-800 p-6" style="background-color:#0f172a">
        <form action="{{ route('turnieje.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Nazwa turnieju --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Nazwa turnieju</label>
                <input type="text" name="Nazwa" value="{{ old('Nazwa') }}"
                       placeholder="np. Wimbledon"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                              text-white placeholder-slate-500 focus:outline-none focus:border-blue-500
                              focus:ring-1 focus:ring-blue-500 transition-colors text-sm">
                @error('Nazwa') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Miasto --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Miasto</label>
                <input type="text" name="Miasto" value="{{ old('Miasto') }}"
                       placeholder="np. Londyn"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                              text-white placeholder-slate-500 focus:outline-none focus:border-blue-500
                              focus:ring-1 focus:ring-blue-500 transition-colors text-sm">
                @error('Miasto') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Kraj --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Kraj</label>
                <select name="Kod_ISO"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                               text-white focus:outline-none focus:border-blue-500
                               focus:ring-1 focus:ring-blue-500 transition-colors text-sm">
                    <option value="">— wybierz kraj —</option>
                    @foreach($kraje as $kraj)
                        <option value="{{ $kraj->Kod_ISO }}"
                                {{ old('Kod_ISO') === $kraj->Kod_ISO ? 'selected' : '' }}>
                            {{ $kraj->Nazwa }} ({{ $kraj->Kod_ISO }})
                        </option>
                    @endforeach
                </select>
                @error('Kod_ISO') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Nawierzchnia --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Nawierzchnia</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['Twarda','Twarda (hala)','Ziemna','Trawa','Dywan'] as $typ)
                        @php
                            $colors = [
                                'Twarda'        => 'border-blue-500/50 bg-blue-500/10 text-blue-300',
                                'Twarda (hala)' => 'border-purple-500/50 bg-purple-500/10 text-purple-300',
                                'Ziemna'        => 'border-orange-500/50 bg-orange-500/10 text-orange-300',
                                'Trawa'         => 'border-green-500/50 bg-green-500/10 text-green-300',
                                'Dywan'         => 'border-yellow-500/50 bg-yellow-500/10 text-yellow-300',
                            ];
                        @endphp
                        <label class="flex items-center gap-2 px-3 py-2.5 rounded-xl border cursor-pointer
                                      transition-all hover:bg-slate-700/50
                                      {{ old('Nawierzchnia') === $typ ? $colors[$typ] : 'border-slate-700 text-slate-400' }}">
                            <input type="radio" name="Nawierzchnia" value="{{ $typ }}"
                                   class="accent-blue-500"
                                   {{ old('Nawierzchnia') === $typ ? 'checked' : '' }}>
                            <span class="text-sm font-medium">{{ $typ }}</span>
                        </label>
                    @endforeach
                </div>
                @error('Nawierzchnia') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Przyciski --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white
                               font-medium text-sm transition-colors">
                    Zapisz turniej
                </button>
                <a href="{{ route('turnieje.index') }}"
                   class="px-6 py-2.5 rounded-xl border border-slate-700 text-slate-400
                          hover:text-white hover:border-slate-600 text-sm transition-colors">
                    Anuluj
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
