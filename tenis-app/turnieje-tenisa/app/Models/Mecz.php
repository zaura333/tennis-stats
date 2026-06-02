<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mecz extends Model
{
    protected $table      = 'MECZ';
    protected $primaryKey = 'ID_Meczu';
    public    $timestamps = false;

    protected $fillable = [
        'ID_Edycji',
        'ID_Rodzaju',
        'Runda',
        'Data',
        'Kort',
        'Czas_trwania_min',
    ];

    // Zwycięska_strona ma polską literę ę – ustawiamy ją osobno
    protected $guarded = [];

    protected $casts = [
        'Data' => 'date',
    ];

    // Edycja turnieju, w której rozegrano mecz
    public function edycja()
    {
        return $this->belongsTo(EdycjaTurnieju::class, 'ID_Edycji', 'ID_Edycji');
    }

    // Rodzaj meczu (singiel, debel itp.)
    public function rodzaj()
    {
        return $this->belongsTo(RodzajMeczu::class, 'ID_Rodzaju', 'ID_Rodzaju');
    }

    // Sety w tym meczu (posortowane)
    public function sety()
    {
        return $this->hasMany(SetMeczu::class, 'ID_Meczu', 'ID_Meczu')
                    ->orderBy('Numer_setu');
    }

    // Wszystkie udziały powiązane z meczem (przez tabelę MECZ_UDZIAL)
    public function udzialy()
    {
        return $this->belongsToMany(
            Udzial::class,
            'MECZ_UDZIAL',
            'ID_Meczu',
            'ID_Udzialu'
        )->withPivot('Strona');
    }

    // Udziały strony A
    public function stronaA()
    {
        return $this->udzialy()->wherePivot('Strona', 'A');
    }

    // Udziały strony B
    public function stronaB()
    {
        return $this->udzialy()->wherePivot('Strona', 'B');
    }

    // Accessor: bezpieczny dostęp do kolumny z polskim znakiem
    public function getZwycieskaStronaAttribute(): ?string
    {
        return $this->attributes['Zwycięska_strona'] ?? null;
    }
}
