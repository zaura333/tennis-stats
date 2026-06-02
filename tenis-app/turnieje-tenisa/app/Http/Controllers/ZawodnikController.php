<?php

namespace App\Http\Controllers;

use App\Models\Kraj;
use App\Models\Plec;
use App\Models\Zawodnik;
use Illuminate\Http\Request;

class ZawodnikController extends Controller
{
    // Lista wszystkich zawodników z ich krajami
    public function index(Request $request)
    {
        $query = Zawodnik::with('kraj')->orderBy('Nazwisko');

        // Wyszukiwanie po nazwisku lub imieniu
        if ($request->filled('szukaj')) {
            $szukaj = $request->input('szukaj');
            $query->where(function ($q) use ($szukaj) {
                $q->where('Nazwisko', 'like', "%$szukaj%")
                  ->orWhere('Imie', 'like', "%$szukaj%");
            });
        }

        // Filtr po płci
        if ($request->filled('plec')) {
            $query->where('Plec', $request->input('plec'));
        }

        $zawodnicy = $query->paginate(24);

        return view('zawodnicy.index', compact('zawodnicy'));
    }

    // Profil zawodnika: dane osobowe + historia turniejów
    public function show(Zawodnik $zawodnik)
    {
        $zawodnik->load([
            'kraj',
            'udzialy.edycja.turniej.kraj',
            'udzialy.statusModel',
        ]);

        // Udziały posortowane malejąco po roku
        $udzialy = $zawodnik->udzialy->sortByDesc(fn ($u) => $u->edycja->Rok);

        return view('zawodnicy.show', compact('zawodnik', 'udzialy'));
    }

    // Formularz dodawania nowego zawodnika
    public function create()
    {
        $kraje = Kraj::orderBy('Nazwa')->get();
        $plcie = Plec::all();

        return view('zawodnicy.create', compact('kraje', 'plcie'));
    }

    // Zapisanie nowego zawodnika do bazy
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Nr_Licencji_ITF' => 'required|max:20|unique:ZAWODNIK,Nr_Licencji_ITF',
            'Imie'            => 'required|max:50',
            'Nazwisko'        => 'required|max:50',
            'Plec'            => 'required|exists:PLEC,Nazwa',
            'Kod_ISO'         => 'required|size:3|exists:KRAJ,Kod_ISO',
            'Data_urodzenia'  => 'required|date|before:today',
        ], [
            'Nr_Licencji_ITF.required' => 'Podaj numer licencji ITF.',
            'Nr_Licencji_ITF.unique'   => 'Ten numer licencji ITF jest już zajęty.',
            'Imie.required'            => 'Podaj imię.',
            'Nazwisko.required'        => 'Podaj nazwisko.',
            'Plec.required'            => 'Wybierz płeć.',
            'Kod_ISO.required'         => 'Wybierz kraj.',
            'Data_urodzenia.required'  => 'Podaj datę urodzenia.',
            'Data_urodzenia.before'    => 'Data urodzenia musi być w przeszłości.',
        ]);

        $zawodnik = Zawodnik::create($validated);

        return redirect()->route('zawodnicy.show', $zawodnik->Nr_Licencji_ITF)
            ->with('success', 'Zawodnik ' . $zawodnik->pelne_nazwisko . ' został dodany.');
    }
}
