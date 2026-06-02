<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table      = 'STATUS';
    protected $primaryKey = 'Status';
    public    $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = ['Status'];

    // Udziały z tym statusem
    public function udzialy()
    {
        return $this->hasMany(Udzial::class, 'Status', 'Status');
    }
}
