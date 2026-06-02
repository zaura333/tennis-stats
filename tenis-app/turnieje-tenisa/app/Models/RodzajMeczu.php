<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RodzajMeczu extends Model
{
    protected $table      = 'RODZAJ_MECZU';
    protected $primaryKey = 'ID_Rodzaju';
    public    $timestamps = false;

    protected $fillable = ['Nazwa', 'Sety_do_wygrania', 'Zawodnikow_na_strone'];

    // Mecze tego rodzaju
    public function mecze()
    {
        return $this->hasMany(Mecz::class, 'ID_Rodzaju', 'ID_Rodzaju');
    }
}
