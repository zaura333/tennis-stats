<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turniej extends Model
{
    protected $table      = 'TURNIEJ';
    protected $primaryKey = 'ID_Turnieju';
    public    $timestamps = false;

    protected $fillable = ['Kod_ISO', 'Nazwa', 'Miasto', 'Nawierzchnia'];

    // Kraj, w którym odbywa się turniej
    public function kraj()
    {
        return $this->belongsTo(Kraj::class, 'Kod_ISO', 'Kod_ISO');
    }

    // Wszystkie edycje roczne tego turnieju
    public function edycje()
    {
        return $this->hasMany(EdycjaTurnieju::class, 'ID_Turnieju', 'ID_Turnieju')
                    ->orderByDesc('Rok');
    }

    // Pomocnicza funkcja: klasy Tailwind dla koloru nawierzchni
    public function kolorNawierzchni(): string
    {
        return match($this->Nawierzchnia) {
            'Twarda'        => 'from-blue-600 to-blue-400',
            'Twarda (hala)' => 'from-purple-600 to-purple-400',
            'Ziemna'        => 'from-orange-600 to-orange-400',
            'Trawa'         => 'from-green-600 to-green-400',
            'Dywan'         => 'from-yellow-600 to-yellow-500',
            default         => 'from-slate-600 to-slate-400',
        };
    }

    // Pomocnicza funkcja: kolor badge'a nawierzchni
    public function badgeNawierzchni(): string
    {
        return match($this->Nawierzchnia) {
            'Twarda'        => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
            'Twarda (hala)' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
            'Ziemna'        => 'bg-orange-500/20 text-orange-300 border-orange-500/30',
            'Trawa'         => 'bg-green-500/20 text-green-300 border-green-500/30',
            'Dywan'         => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
            default         => 'bg-slate-500/20 text-slate-300 border-slate-500/30',
        };
    }
}
