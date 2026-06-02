<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Udzial extends Model
{
    protected $table      = 'UDZIAL';
    protected $primaryKey = 'ID_Udzialu';
    public    $timestamps = false;

    protected $fillable = [
        'Nr_Licencji_ITF',
        'ID_Edycji',
        'Status',
        'Rozstawienie',
        'Ranking_w_momencie_rejestracji',
        'Data_rejestracji',
    ];

    protected $casts = [
        'Data_rejestracji' => 'date',
    ];

    // Zawodnik, którego dotyczy udział
    public function zawodnik()
    {
        return $this->belongsTo(Zawodnik::class, 'Nr_Licencji_ITF', 'Nr_Licencji_ITF');
    }

    // Edycja turnieju, w której zawodnik bierze udział
    public function edycja()
    {
        return $this->belongsTo(EdycjaTurnieju::class, 'ID_Edycji', 'ID_Edycji');
    }

    // Status słownikowy udziału
    public function statusModel()
    {
        return $this->belongsTo(Status::class, 'Status', 'Status');
    }

    // Mecze, w których uczestniczył zawodnik w ramach tego udziału
    public function mecze()
    {
        return $this->belongsToMany(
            Mecz::class,
            'MECZ_UDZIAL',
            'ID_Udzialu',
            'ID_Meczu'
        )->withPivot('Strona');
    }
}
