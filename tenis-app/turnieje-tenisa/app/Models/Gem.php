<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gem extends Model
{
    protected $table      = 'GEM';
    protected $primaryKey = 'ID_Gemu';
    public    $timestamps = false;

    protected $fillable = [
        'ID_Setu', 'Numer_gemu', 'Serwujacy', 'Punkty_A', 'Punkty_B',
    ];

    // Set, do którego należy ten gem
    public function set()
    {
        return $this->belongsTo(SetMeczu::class, 'ID_Setu', 'ID_Setu');
    }
}
