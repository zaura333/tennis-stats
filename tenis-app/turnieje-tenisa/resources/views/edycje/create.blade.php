@extends('layouts.app')

@section('title', 'Nowa edycja – ' . $turniej->Nazwa)
@section('heading', 'Nowa edycja – ' . $turniej->Nazwa)
@section('subheading', 'Ustaw rok, daty i dodaj uczestników')

@section('content')

<form action="{{ route('edycje.store') }}" method="POST" id="formEdycja">
@csrf
<input type="hidden" name="ID_Turnieju" value="{{ $turniej->ID_Turnieju }}">

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-5xl">

    {{-- Lewa kolumna: Dane edycji --}}
    <div class="rounded-2xl border border-slate-800 p-6 space-y-5" style="background-color:#0f172a">
        <h2 class="text-base font-semibold text-white mb-4">Dane edycji</h2>

        {{-- Rok --}}
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Rok</label>
            <input type="number" name="Rok" value="{{ old('Rok', date('Y')) }}"
                   min="1877" max="2100"
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                          text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
            @error('Rok') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Data rozpoczęcia --}}
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Data rozpoczęcia</label>
            <input type="date" name="Data_rozpoczecia" value="{{ old('Data_rozpoczecia') }}"
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                          text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
            @error('Data_rozpoczecia') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Data zakończenia --}}
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5">Data zakończenia</label>
            <input type="date" name="Data_zakonczenia" value="{{ old('Data_zakonczenia') }}"
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-800 border border-slate-700
                          text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
            @error('Data_zakonczenia') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-2 flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition-colors">
                Zapisz edycję
            </button>
            <a href="{{ route('turnieje.show', $turniej->ID_Turnieju) }}"
               class="px-6 py-2.5 rounded-xl border border-slate-700 text-slate-400 hover:text-white text-sm transition-colors">
                Anuluj
            </a>
        </div>
    </div>

    {{-- Prawa kolumna: Uczestnicy --}}
    <div class="rounded-2xl border border-slate-800 p-6" style="background-color:#0f172a">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-white">Uczestnicy</h2>
            <div class="flex gap-2">
                <button type="button" onclick="dodajIstniejacego()"
                        class="text-xs px-3 py-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-white transition-colors">
                    + Istniejący
                </button>
                <button type="button" onclick="dodajNowego()"
                        class="text-xs px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-600 text-white transition-colors">
                    + Nowy zawodnik
                </button>
            </div>
        </div>

        {{-- Lista uczestników (dynamicznie dodawana przez JS) --}}
        <div id="lista-uczestnikow" class="space-y-3 max-h-[500px] overflow-y-auto scrollbar-thin">
            <p class="text-slate-500 text-sm text-center py-8" id="pusty-komunikat">
                Brak uczestników. Kliknij przycisk, aby dodać.
            </p>
        </div>
    </div>

</div>
</form>

{{-- Przygotowanie danych dla JavaScript --}}
@php
    $jsZawodnicy = $zawodnicy->map(fn($z) => [
        'nr'       => $z->Nr_Licencji_ITF,
        'imie'     => $z->Imie,
        'nazwisko' => $z->Nazwisko,
        'kraj'     => $z->kraj->Kod_ISO ?? '',
    ])->values()->toArray();

    $jsStatusy = $statusy->pluck('Status')->toArray();
    $jsKraje   = $kraje->map(fn($k) => ['kod' => $k->Kod_ISO, 'nazwa' => $k->Nazwa])->values()->toArray();
    $jsPlcie   = $plcie->pluck('Nazwa')->toArray();
@endphp

<script>
    // Dane z serwera przekazane do JavaScript
    const zawodnicy = {!! json_encode($jsZawodnicy) !!};
    const statusy   = {!! json_encode($jsStatusy) !!};
    const kraje     = {!! json_encode($jsKraje) !!};
    const plcie     = {!! json_encode($jsPlcie) !!};

    // Licznik wierszy uczestników
    let licznikWierszy = 0;

    // Wspólne pola dla wiersza udziału (status, rozstawienie, ranking)
    function wspolnePola(idx) {
        return `
            <div class="grid grid-cols-3 gap-2 mt-2">
                <div>
                    <label class="text-xs text-slate-500">Status</label>
                    <select name="uczestnicy[${idx}][Status]"
                            class="w-full mt-1 px-2 py-1.5 rounded-lg bg-slate-700 border border-slate-600
                                   text-white text-xs focus:outline-none focus:border-blue-500">
                        ${statusy.map(s => `<option value="${s}">${s}</option>`).join('')}
                    </select>
                </div>
                <div>
                    <label class="text-xs text-slate-500">Rozst.</label>
                    <input type="number" name="uczestnicy[${idx}][Rozstawienie]" value="1" min="1"
                           class="w-full mt-1 px-2 py-1.5 rounded-lg bg-slate-700 border border-slate-600
                                  text-white text-xs focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="text-xs text-slate-500">Ranking</label>
                    <input type="number" name="uczestnicy[${idx}][Ranking]" value="100" min="1"
                           class="w-full mt-1 px-2 py-1.5 rounded-lg bg-slate-700 border border-slate-600
                                  text-white text-xs focus:outline-none focus:border-blue-500">
                </div>
            </div>
            <input type="hidden" name="uczestnicy[${idx}][Data_rejestracji]"
                   value="${new Date().toISOString().split('T')[0]}">
        `;
    }

    // Dodawanie istniejącego zawodnika
    function dodajIstniejacego() {
        const idx = licznikWierszy++;
        ukryjPustyKomunikat();

        const opcje = zawodnicy.map(z =>
            `<option value="${z.nr}">${z.nazwisko} ${z.imie} (${z.kraj})</option>`
        ).join('');

        const html = `
            <div id="wiersz-${idx}" class="bg-slate-800 rounded-xl p-3 border border-slate-700">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-400">Istniejący zawodnik</span>
                    <button type="button" onclick="usunWiersz(${idx})"
                            class="text-red-400 hover:text-red-300 text-xs">✕</button>
                </div>
                <input type="hidden" name="uczestnicy[${idx}][typ]" value="istniejacy">
                <select name="uczestnicy[${idx}][Nr_Licencji_ITF]"
                        class="w-full px-3 py-2 rounded-lg bg-slate-700 border border-slate-600
                               text-white text-sm focus:outline-none focus:border-blue-500">
                    <option value="">— wybierz zawodnika —</option>
                    ${opcje}
                </select>
                ${wspolnePola(idx)}
            </div>
        `;

        document.getElementById('lista-uczestnikow').insertAdjacentHTML('beforeend', html);
    }

    // Dodawanie nowego zawodnika "w locie"
    function dodajNowego() {
        const idx = licznikWierszy++;
        ukryjPustyKomunikat();

        const krajeOpcje = kraje.map(k => `<option value="${k.kod}">${k.nazwa} (${k.kod})</option>`).join('');
        const plecOpcje  = plcie.map(p => `<option value="${p}">${p}</option>`).join('');

        const html = `
            <div id="wiersz-${idx}" class="bg-emerald-900/20 rounded-xl p-3 border border-emerald-700/40">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-emerald-400">Nowy zawodnik</span>
                    <button type="button" onclick="usunWiersz(${idx})"
                            class="text-red-400 hover:text-red-300 text-xs">✕</button>
                </div>
                <input type="hidden" name="uczestnicy[${idx}][typ]" value="nowy">
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <input type="text" name="uczestnicy[${idx}][Nr_Licencji_ITF]"
                           placeholder="Nr licencji ITF"
                           class="px-2 py-1.5 rounded-lg bg-slate-700 border border-slate-600
                                  text-white text-xs placeholder-slate-500 focus:outline-none focus:border-blue-500">
                    <input type="date" name="uczestnicy[${idx}][Data_urodzenia]"
                           class="px-2 py-1.5 rounded-lg bg-slate-700 border border-slate-600
                                  text-white text-xs focus:outline-none focus:border-blue-500">
                    <input type="text" name="uczestnicy[${idx}][Imie]"
                           placeholder="Imię"
                           class="px-2 py-1.5 rounded-lg bg-slate-700 border border-slate-600
                                  text-white text-xs placeholder-slate-500 focus:outline-none focus:border-blue-500">
                    <input type="text" name="uczestnicy[${idx}][Nazwisko]"
                           placeholder="Nazwisko"
                           class="px-2 py-1.5 rounded-lg bg-slate-700 border border-slate-600
                                  text-white text-xs placeholder-slate-500 focus:outline-none focus:border-blue-500">
                    <select name="uczestnicy[${idx}][Plec]"
                            class="px-2 py-1.5 rounded-lg bg-slate-700 border border-slate-600
                                   text-white text-xs focus:outline-none focus:border-blue-500">
                        ${plecOpcje}
                    </select>
                    <select name="uczestnicy[${idx}][Kod_ISO]"
                            class="px-2 py-1.5 rounded-lg bg-slate-700 border border-slate-600
                                   text-white text-xs focus:outline-none focus:border-blue-500">
                        <option value="">— kraj —</option>
                        ${krajeOpcje}
                    </select>
                </div>
                ${wspolnePola(idx)}
            </div>
        `;

        document.getElementById('lista-uczestnikow').insertAdjacentHTML('beforeend', html);
    }

    // Usuwanie wiersza uczestnika
    function usunWiersz(idx) {
        document.getElementById('wiersz-' + idx).remove();
        if (document.querySelectorAll('#lista-uczestnikow > div').length === 0) {
            document.getElementById('pusty-komunikat').style.display = 'block';
        }
    }

    function ukryjPustyKomunikat() {
        const el = document.getElementById('pusty-komunikat');
        if (el) el.style.display = 'none';
    }
</script>

@endsection
