<?php

namespace App\Http\Controllers;

use App\Models\EdycjaTurnieju;
use App\Models\Mecz;
use App\Models\RodzajMeczu;
use App\Models\SetMeczu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeczController extends Controller
{
    // Dostępne rundy w turnieju tenisowym
    public const RUNDY = [
        'Kwalifikacje R1', 'Kwalifikacje R2', 'Kwalifikacje R3',
        '1. runda', '2. runda', '3. runda', '4. runda',
        '1/8 finału', 'Ćwierćfinał', 'Półfinał', 'Finał',
    ];

    // Wyświetlenie szczegółów meczu: zawodnicy, sety, wynik
    public function show(Mecz $mecz)
    {
        $mecz->load([
            'edycja.turniej',
            'rodzaj',
            'sety',
            'stronaA.zawodnik.kraj',
            'stronaB.zawodnik.kraj',
        ]);

        return view('mecze.show', compact('mecz'));
    }

    // Formularz dodawania nowego meczu do edycji
    public function create(EdycjaTurnieju $edycja)
    {
        $edycja->load(['turniej', 'udzialy.zawodnik.kraj']);
        $rodzaje = RodzajMeczu::all();
        $rundy   = self::RUNDY;

        return view('mecze.create', compact('edycja', 'rodzaje', 'rundy'));
    }

    // Zapisanie meczu z uczestnikami i wynikami setów
    public function store(Request $request)
    {
        $request->validate([
            'ID_Edycji'    => 'required|exists:EDYCJA_TURNIEJU,ID_Edycji',
            'ID_Rodzaju'   => 'required|exists:RODZAJ_MECZU,ID_Rodzaju',
            'Runda'        => 'required|in:' . implode(',', self::RUNDY),
            'Data'         => 'required|date',
            'Kort'         => 'required|max:50',
            'strona_a'     => 'required|array|min:1',
            'strona_a.*'   => 'exists:UDZIAL,ID_Udzialu',
            'strona_b'     => 'required|array|min:1',
            'strona_b.*'   => 'exists:UDZIAL,ID_Udzialu',
        ], [
            'Runda.required'   => 'Wybierz rundę.',
            'Kort.required'    => 'Podaj nazwę kortu.',
            'strona_a.required' => 'Wybierz zawodników strony A.',
            'strona_b.required' => 'Wybierz zawodników strony B.',
        ]);

        DB::transaction(function () use ($request, &$mecz) {
            // Tworzenie rekordu meczu
            $mecz = new Mecz([
                'ID_Edycji'  => $request->ID_Edycji,
                'ID_Rodzaju' => $request->ID_Rodzaju,
                'Runda'      => $request->Runda,
                'Data'       => $request->Data,
                'Kort'       => $request->Kort,
                'Czas_trwania_min' => $request->filled('Czas_trwania_min')
                    ? $request->Czas_trwania_min : null,
            ]);

            // Ustawiamy kolumnę z polskim znakiem przed zapisem
            $mecz->setAttribute('Zwycięska_strona', $request->input('Zwycieska_strona') ?: null);
            $mecz->save();

            // Przypisanie uczestników strony A
            foreach ($request->strona_a as $udzialId) {
                DB::table('MECZ_UDZIAL')->insert([
                    'ID_Meczu'   => $mecz->ID_Meczu,
                    'ID_Udzialu' => $udzialId,
                    'Strona'     => 'A',
                ]);
            }

            // Przypisanie uczestników strony B
            foreach ($request->strona_b as $udzialId) {
                DB::table('MECZ_UDZIAL')->insert([
                    'ID_Meczu'   => $mecz->ID_Meczu,
                    'ID_Udzialu' => $udzialId,
                    'Strona'     => 'B',
                ]);
            }

            // Zapisywanie wyników setów przesłanych z formularza
            $sety = $request->input('sety', []);
            foreach ($sety as $numer => $set) {
                if (isset($set['Wynik_A']) && isset($set['Wynik_B'])) {
                    SetMeczu::create([
                        'ID_Meczu'   => $mecz->ID_Meczu,
                        'Numer_setu' => $numer + 1,
                        'Wynik_A'    => $set['Wynik_A'],
                        'Wynik_B'    => $set['Wynik_B'],
                        'Tie_break'  => isset($set['Tie_break']) ? 1 : 0,
                    ]);
                }
            }
        });

        return redirect()->route('mecze.show', $mecz->ID_Meczu)
            ->with('success', 'Mecz został dodany.');
    }
}
