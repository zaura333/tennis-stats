<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zawodnik extends Model
{
    protected $table      = 'ZAWODNIK';
    protected $primaryKey = 'Nr_Licencji_ITF';
    public    $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = [
        'Nr_Licencji_ITF', 'Plec', 'Kod_ISO', 'Imie', 'Nazwisko', 'Data_urodzenia',
    ];

    protected $casts = [
        'Data_urodzenia' => 'date',
    ];

    // Kraj reprezentowany przez zawodnika
    public function kraj()
    {
        return $this->belongsTo(Kraj::class, 'Kod_ISO', 'Kod_ISO');
    }

    // Płeć zawodnika (relacja do słownika)
    public function plecModel()
    {
        return $this->belongsTo(Plec::class, 'Plec', 'Nazwa');
    }

    // Wszystkie udziały w turniejach
    public function udzialy()
    {
        return $this->hasMany(Udzial::class, 'Nr_Licencji_ITF', 'Nr_Licencji_ITF');
    }

    // Accessor: pełne imię i nazwisko
    public function getPelneNazwiskoAttribute(): string
    {
        return $this->Imie . ' ' . $this->Nazwisko;
    }

    // Accessor: wiek zawodnika na podstawie daty urodzenia
    public function getWiekAttribute(): int
    {
        return $this->Data_urodzenia->age;
    }
}
