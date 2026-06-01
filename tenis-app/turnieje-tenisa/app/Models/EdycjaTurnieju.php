<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdycjaTurnieju extends Model
{
    protected $table      = 'EDYCJA_TURNIEJU';
    protected $primaryKey = 'ID_Edycji';
    public    $timestamps = false;

    protected $fillable = [
        'ID_Turnieju', 'Rok', 'Data_rozpoczecia', 'Data_zakonczenia',
    ];

    protected $casts = [
        'Data_rozpoczecia' => 'date',
        'Data_zakonczenia' => 'date',
    ];

    // Turniej, do którego należy ta edycja
    public function turniej()
    {
        return $this->belongsTo(Turniej::class, 'ID_Turnieju', 'ID_Turnieju');
    }

    // Mecze rozegrane w tej edycji
    public function mecze()
    {
        return $this->hasMany(Mecz::class, 'ID_Edycji', 'ID_Edycji');
    }

    // Udziały (zgłoszenia zawodników) w tej edycji
    public function udzialy()
    {
        return $this->hasMany(Udzial::class, 'ID_Edycji', 'ID_Edycji')
                    ->orderBy('Rozstawienie');
    }
}
