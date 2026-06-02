<?php

namespace App\Http\Controllers;

use App\Models\Kraj;
use App\Models\Turniej;
use Illuminate\Http\Request;

class TurniejController extends Controller
{
    // Wyświetlenie listy wszystkich turniejów jako karty
    public function index()
    {
        $turnieje = Turniej::with(['kraj', 'edycje'])
            ->withCount('edycje')
            ->orderBy('Nazwa')
            ->get();

        return view('turnieje.index', compact('turnieje'));
    }

    // Wyświetlenie szczegółów turnieju wraz z jego edycjami
    public function show(Turniej $turniej)
    {
        $turniej->load(['kraj', 'edycje.mecze', 'edycje.udzialy']);

        return view('turnieje.show', compact('turniej'));
    }

    // Formularz tworzenia nowego turnieju
    public function create()
    {
        $kraje = Kraj::orderBy('Nazwa')->get();

        return view('turnieje.create', compact('kraje'));
    }

    // Zapisanie nowego turnieju do bazy
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Kod_ISO'      => 'required|size:3|exists:KRAJ,Kod_ISO',
            'Nazwa'        => 'required|max:100',
            'Miasto'       => 'required|max:100',
            'Nawierzchnia' => 'required|in:Twarda,Twarda (hala),Ziemna,Trawa,Dywan',
        ], [
            'Kod_ISO.required'      => 'Wybierz kraj.',
            'Kod_ISO.exists'        => 'Wybrany kraj nie istnieje w bazie.',
            'Nazwa.required'        => 'Podaj nazwę turnieju.',
            'Miasto.required'       => 'Podaj miasto.',
            'Nawierzchnia.required' => 'Wybierz nawierzchnię.',
            'Nawierzchnia.in'       => 'Nieprawidłowy typ nawierzchni.',
        ]);

        $turniej = Turniej::create($validated);

        return redirect()->route('turnieje.show', $turniej->ID_Turnieju)
            ->with('success', 'Turniej "' . $turniej->Nazwa . '" został dodany.');
    }
}
