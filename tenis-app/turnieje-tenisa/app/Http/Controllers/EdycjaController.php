<?php

namespace App\Http\Controllers;

use App\Models\EdycjaTurnieju;
use App\Models\Kraj;
use App\Models\Plec;
use App\Models\Status;
use App\Models\Turniej;
use App\Models\Udzial;
use App\Models\Zawodnik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EdycjaController extends Controller
{
    // Wyświetlenie szczegółów edycji: mecze, zawodnicy, rundy
    public function show(EdycjaTurnieju $edycja)
    {
        $edycja->load([
            'turniej.kraj',
            'udzialy.zawodnik.kraj',
            'mecze.rodzaj',
            'mecze.stronaA.zawodnik',
            'mecze.stronaB.zawodnik',
            'mecze.sety',
        ]);

        // Grupowanie meczów według rundy
        $meczePoRundach = $edycja->mecze->groupBy('Runda');

        // Kolejność rund dla wyświetlenia
        $kolejnoscRund = [
            'Kwalifikacje R1', 'Kwalifikacje R2', 'Kwalifikacje R3',
            '1. runda', '2. runda', '3. runda', '4. runda',
            '1/8 finału', 'Ćwierćfinał', 'Półfinał', 'Finał',
        ];

        return view('edycje.show', compact('edycja', 'meczePoRundach', 'kolejnoscRund'));
    }

    // Formularz tworzenia nowej edycji turnieju
    public function create(Turniej $turniej)
    {
        $statusy  = Status::all();
        $zawodnicy = Zawodnik::with('kraj')->orderBy('Nazwisko')->get();
        $kraje    = Kraj::orderBy('Nazwa')->get();
        $plcie    = Plec::all();

        return view('edycje.create', compact('turniej', 'statusy', 'zawodnicy', 'kraje', 'plcie'));
    }

    // Zapisanie nowej edycji razem z uczestnikami
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ID_Turnieju'       => 'required|exists:TURNIEJ,ID_Turnieju',
            'Rok'               => 'required|integer|between:1877,2100',
            'Data_rozpoczecia'  => 'required|date',
            'Data_zakonczenia'  => 'required|date|after_or_equal:Data_rozpoczecia',
        ], [
            'Rok.required'              => 'Podaj rok edycji.',
            'Rok.between'               => 'Rok musi być między 1877 a 2100.',
            'Data_rozpoczecia.required' => 'Podaj datę rozpoczęcia.',
            'Data_zakonczenia.required' => 'Podaj datę zakończenia.',
            'Data_zakonczenia.after_or_equal' => 'Data zakończenia musi być po dacie rozpoczęcia.',
        ]);

        DB::transaction(function () use ($request, $validated) {
            // Tworzenie rekordu edycji
            $edycja = EdycjaTurnieju::create($validated);

            // Przetwarzanie listy uczestników przesłanej z formularza
            $uczestnicy = $request->input('uczestnicy', []);

            foreach ($uczestnicy as $uczestnik) {
                // Jeśli zawodnik jest nowy – najpierw go zapisujemy w bazie
                if (($uczestnik['typ'] ?? '') === 'nowy') {
                    $zawodnik = Zawodnik::create([
                        'Nr_Licencji_ITF' => $uczestnik['Nr_Licencji_ITF'],
                        'Imie'            => $uczestnik['Imie'],
                        'Nazwisko'        => $uczestnik['Nazwisko'],
                        'Plec'            => $uczestnik['Plec'],
                        'Kod_ISO'         => $uczestnik['Kod_ISO'],
                        'Data_urodzenia'  => $uczestnik['Data_urodzenia'],
                    ]);
                    $licencja = $zawodnik->Nr_Licencji_ITF;
                } else {
                    // Istniejący zawodnik – używamy przesłanego numeru licencji
                    $licencja = $uczestnik['Nr_Licencji_ITF'];
                }

                // Tworzenie rekordu udziału dla zawodnika w tej edycji
                Udzial::create([
                    'Nr_Licencji_ITF'               => $licencja,
                    'ID_Edycji'                     => $edycja->ID_Edycji,
                    'Status'                        => $uczestnik['Status'],
                    'Rozstawienie'                  => $uczestnik['Rozstawienie'] ?? 1,
                    'Ranking_w_momencie_rejestracji' => $uczestnik['Ranking'] ?? 1,
                    'Data_rejestracji'              => $uczestnik['Data_rejestracji'] ?? now()->toDateString(),
                ]);
            }

            $this->edycjaId = $edycja->ID_Edycji;
        });

        // Pobieramy ID edycji po transakcji
        $edycja = EdycjaTurnieju::where('ID_Turnieju', $validated['ID_Turnieju'])
            ->where('Rok', $validated['Rok'])
            ->first();

        return redirect()->route('edycje.show', $edycja->ID_Edycji)
            ->with('success', 'Edycja ' . $validated['Rok'] . ' została dodana.');
    }
}
