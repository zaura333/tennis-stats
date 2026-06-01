@extends('layouts.app')

@section('title', 'Nowy mecz – TennisArchive')
@section('heading', 'Nowy mecz')
@section('subheading', $edycja->turniej->Nazwa . ' ' . $edycja->Rok)

@section('header-actions')
    <a href="{{ route('edycje.show', $edycja->ID_Edycji) }}"
       class="px-4 py-2 rounded-xl border border-slate-700 text-slate-400 hover:text-white text-sm transition-colors">
        ← Wróć do edycji
    </a>
@endsection

@section('content')

<form action="{{ route('mecze.store') }}" method="POST" class="max-w-3xl">
@csrf
<input type="hidden" name="ID_Edycji" value="{{ $edycja->ID_Edycji }}">

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Lewa kolumna: Dane meczu --}}
    <div class="rounded-2xl border border-slate-800 p-6 space-y-5" style="background-color:#0f172a">
        <h2 class="text-base font-semibold text-white">Dane meczu</h2>

        {{-- Runda --}}
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Runda</label>
            <select name="Runda"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                           text-white focus:outline-none focus:border-blue-500 text-sm">
                <option value="">— wybierz rundę —</option>
                @foreach($rundy as $runda)
                    <option value="{{ $runda }}" {{ old('Runda') === $runda ? 'selected' : '' }}>
                        {{ $runda }}
                    </option>
                @endforeach
            </select>
            @error('Runda') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Rodzaj meczu --}}
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Rodzaj meczu</label>
            <select name="ID_Rodzaju"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                           text-white focus:outline-none focus:border-blue-500 text-sm">
                <option value="">— wybierz rodzaj —</option>
                @foreach($rodzaje as $rodzaj)
                    <option value="{{ $rodzaj->ID_Rodzaju }}"
                            {{ old('ID_Rodzaju') == $rodzaj->ID_Rodzaju ? 'selected' : '' }}>
                        {{ $rodzaj->Nazwa }}
                    </option>
                @endforeach
            </select>
            @error('ID_Rodzaju') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Data i Kort --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Data</label>
                <input type="date" name="Data" value="{{ old('Data') }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                              text-white focus:outline-none focus:border-blue-500 text-sm">
                @error('Data') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Kort</label>
                <input type="text" name="Kort" value="{{ old('Kort') }}"
                       placeholder="np. Court 1"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                              text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 text-sm">
                @error('Kort') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Czas trwania --}}
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">
                Czas trwania (min) <span class="text-slate-500 font-normal">– opcjonalnie</span>
            </label>
            <input type="number" name="Czas_trwania_min" value="{{ old('Czas_trwania_min') }}"
                   min="1" placeholder="np. 120"
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                          text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 text-sm">
        </div>

        {{-- Zwycięska strona --}}
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">
                Zwycięska strona <span class="text-slate-500 font-normal">– opcjonalnie</span>
            </label>
            <div class="flex gap-3">
                @foreach(['A', 'B'] as $str)
                    <label class="flex items-center gap-2 px-5 py-2.5 rounded-xl border cursor-pointer transition-all
                                  {{ old('Zwycieska_strona') === $str ? 'border-emerald-500/50 bg-emerald-500/10 text-emerald-300' : 'border-slate-700 text-slate-400 hover:bg-slate-700/50' }}">
                        <input type="radio" name="Zwycieska_strona" value="{{ $str }}"
                               class="accent-emerald-500"
                               {{ old('Zwycieska_strona') === $str ? 'checked' : '' }}>
                        <span class="text-sm font-bold">Strona {{ $str }}</span>
                    </label>
                @endforeach
                <label class="flex items-center gap-2 px-5 py-2.5 rounded-xl border cursor-pointer transition-all
                              border-slate-700 text-slate-400 hover:bg-slate-700/50">
                    <input type="radio" name="Zwycieska_strona" value=""
                           class="accent-slate-500" checked>
                    <span class="text-sm">Brak</span>
                </label>
            </div>
        </div>
    </div>

    {{-- Prawa kolumna: Zawodnicy i sety --}}
    <div class="space-y-5">

        {{-- Strona A --}}
        <div class="rounded-2xl border border-blue-700/30 p-5" style="background-color:#0f172a">
            <h3 class="text-sm font-semibold text-blue-400 mb-3">Strona A</h3>
            <select name="strona_a[]" multiple
                    class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700
                           text-white text-sm focus:outline-none focus:border-blue-500 min-h-[100px]">
                @foreach($edycja->udzialy as $udzial)
                    <option value="{{ $udzial->ID_Udzialu }}">
                        [{{ $udzial->Rozstawienie }}] {{ $udzial->zawodnik->pelne_nazwisko }}
                        ({{ $udzial->zawodnik->Kod_ISO }})
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-slate-500 mt-1">Ctrl+klik = wybór wielu (debel)</p>
            @error('strona_a') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Strona B --}}
        <div class="rounded-2xl border border-orange-700/30 p-5" style="background-color:#0f172a">
            <h3 class="text-sm font-semibold text-orange-400 mb-3">Strona B</h3>
            <select name="strona_b[]" multiple
                    class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700
                           text-white text-sm focus:outline-none focus:border-orange-500 min-h-[100px]">
                @foreach($edycja->udzialy as $udzial)
                    <option value="{{ $udzial->ID_Udzialu }}">
                        [{{ $udzial->Rozstawienie }}] {{ $udzial->zawodnik->pelne_nazwisko }}
                        ({{ $udzial->zawodnik->Kod_ISO }})
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-slate-500 mt-1">Ctrl+klik = wybór wielu (debel)</p>
            @error('strona_b') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Wyniki setów --}}
        <div class="rounded-2xl border border-slate-800 p-5" style="background-color:#0f172a">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-white">Wyniki setów</h3>
                <button type="button" onclick="dodajSet()"
                        class="text-xs px-3 py-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-white transition-colors">
                    + Dodaj set
                </button>
            </div>

            <div id="sety-container" class="space-y-2">
                <p class="text-slate-500 text-xs text-center py-3" id="brak-setow">
                    Kliknij "Dodaj set" aby wpisać wyniki.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Przyciski zapisu --}}
<div class="flex items-center gap-3 mt-6">
    <button type="submit"
            class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition-colors">
        Zapisz mecz
    </button>
    <a href="{{ route('edycje.show', $edycja->ID_Edycji) }}"
       class="px-6 py-2.5 rounded-xl border border-slate-700 text-slate-400 hover:text-white text-sm transition-colors">
        Anuluj
    </a>
</div>
</form>

<script>
    let licznikSetow = 0;

    // Dodawanie wiersza z wynikiem setu
    function dodajSet() {
        const idx = licznikSetow++;
        const brak = document.getElementById('brak-setow');
        if (brak) brak.style.display = 'none';

        const html = `
            <div id="set-${idx}" class="flex items-center gap-3 bg-slate-800 rounded-xl px-3 py-2">
                <span class="text-xs text-slate-400 font-semibold w-10">Set ${idx + 1}</span>
                <div class="flex items-center gap-2 flex-1">
                    <input type="number" name="sety[${idx}][Wynik_A]" min="0" max="7"
                           placeholder="A" value=""
                           class="w-14 px-2 py-1.5 rounded-lg bg-slate-700 border border-slate-600
                                  text-white text-center text-sm focus:outline-none focus:border-blue-500">
                    <span class="text-slate-500">—</span>
                    <input type="number" name="sety[${idx}][Wynik_B]" min="0" max="7"
                           placeholder="B" value=""
                           class="w-14 px-2 py-1.5 rounded-lg bg-slate-700 border border-slate-600
                                  text-white text-center text-sm focus:outline-none focus:border-blue-500">
                </div>
                <label class="flex items-center gap-1.5 text-xs text-slate-400 cursor-pointer">
                    <input type="checkbox" name="sety[${idx}][Tie_break]" value="1"
                           class="accent-yellow-400 rounded">
                    Tie-break
                </label>
                <button type="button" onclick="usunSet(${idx})"
                        class="text-red-400 hover:text-red-300 text-xs px-1">✕</button>
            </div>
        `;
        document.getElementById('sety-container').insertAdjacentHTML('beforeend', html);
    }

    // Usuwanie setu
    function usunSet(idx) {
        const el = document.getElementById('set-' + idx);
        if (el) el.remove();
        if (document.querySelectorAll('#sety-container > div').length === 0) {
            document.getElementById('brak-setow').style.display = 'block';
        }
    }
</script>

@endsection
