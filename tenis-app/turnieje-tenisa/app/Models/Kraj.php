<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kraj extends Model
{
    protected $table      = 'KRAJ';
    protected $primaryKey = 'Kod_ISO';
    public    $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = ['Kod_ISO', 'Nazwa', 'Kontynent'];

    // Turnieje odbywające się w tym kraju
    public function turnieje()
    {
        return $this->hasMany(Turniej::class, 'Kod_ISO', 'Kod_ISO');
    }

    // Zawodnicy reprezentujący ten kraj
    public function zawodnicy()
    {
        return $this->hasMany(Zawodnik::class, 'Kod_ISO', 'Kod_ISO');
    }
}
