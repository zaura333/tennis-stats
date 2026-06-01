<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plec extends Model
{
    protected $table      = 'PLEC';
    protected $primaryKey = 'Nazwa';
    public    $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = ['Nazwa'];

    // Zawodnicy danej płci
    public function zawodnicy()
    {
        return $this->hasMany(Zawodnik::class, 'Plec', 'Nazwa');
    }
}
