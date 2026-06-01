@extends('layouts.app')

@section('title', 'Nowy zawodnik – TennisArchive')
@section('heading', 'Nowy zawodnik')
@section('subheading', 'Wypełnij dane zawodnika')

@section('content')

<div class="max-w-lg">
    <div class="rounded-2xl border border-slate-800 p-6" style="background-color:#0f172a">
        <form action="{{ route('zawodnicy.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Numer licencji ITF --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Numer licencji ITF</label>
                <input type="text" name="Nr_Licencji_ITF" value="{{ old('Nr_Licencji_ITF') }}"
                       placeholder="np. ITF-POL-12345"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                              text-white font-mono placeholder-slate-500 focus:outline-none
                              focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                @error('Nr_Licencji_ITF') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Imię i Nazwisko --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Imię</label>
                    <input type="text" name="Imie" value="{{ old('Imie') }}"
                           placeholder="Rafał"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                                  text-white placeholder-slate-500 focus:outline-none
                                  focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                    @error('Imie') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Nazwisko</label>
                    <input type="text" name="Nazwisko" value="{{ old('Nazwisko') }}"
                           placeholder="Nadal"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                                  text-white placeholder-slate-500 focus:outline-none
                                  focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                    @error('Nazwisko') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Data urodzenia --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Data urodzenia</label>
                <input type="date" name="Data_urodzenia" value="{{ old('Data_urodzenia') }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                              text-white focus:outline-none focus:border-blue-500
                              focus:ring-1 focus:ring-blue-500 text-sm">
                @error('Data_urodzenia') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Płeć --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Płeć</label>
                <div class="flex gap-3">
                    @foreach($plcie as $p)
                        <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer
                                      transition-all hover:bg-slate-700/50
                                      {{ old('Plec') === $p->Nazwa ? 'border-blue-500/50 bg-blue-500/10 text-blue-300' : 'border-slate-700 text-slate-400' }}">
                            <input type="radio" name="Plec" value="{{ $p->Nazwa }}"
                                   class="accent-blue-500"
                                   {{ old('Plec') === $p->Nazwa ? 'checked' : '' }}>
                            <span class="text-sm">{{ $p->Nazwa }}</span>
                        </label>
                    @endforeach
                </div>
                @error('Plec') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Kraj --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Kraj</label>
                <select name="Kod_ISO"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                               text-white focus:outline-none focus:border-blue-500
                               focus:ring-1 focus:ring-blue-500 text-sm">
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

            {{-- Przyciski --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white
                               font-medium text-sm transition-colors">
                    Zapisz zawodnika
                </button>
                <a href="{{ route('zawodnicy.index') }}"
                   class="px-6 py-2.5 rounded-xl border border-slate-700 text-slate-400
                          hover:text-white hover:border-slate-600 text-sm transition-colors">
                    Anuluj
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
