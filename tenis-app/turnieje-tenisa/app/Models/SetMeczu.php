<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetMeczu extends Model
{
    protected $table      = 'SET_MECZU';
    protected $primaryKey = 'ID_Setu';
    public    $timestamps = false;

    protected $fillable = [
        'ID_Meczu', 'Numer_setu', 'Wynik_A', 'Wynik_B', 'Tie_break',
    ];

    protected $casts = [
        'Tie_break' => 'boolean',
    ];

    // Mecz, do którego należy ten set
    public function mecz()
    {
        return $this->belongsTo(Mecz::class, 'ID_Meczu', 'ID_Meczu');
    }

    // Gemy w tym secie
    public function gemy()
    {
        return $this->hasMany(Gem::class, 'ID_Setu', 'ID_Setu')
                    ->orderBy('Numer_gemu');
    }
}
