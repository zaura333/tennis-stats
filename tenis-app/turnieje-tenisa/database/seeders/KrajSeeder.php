<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KrajSeeder extends Seeder
{
    // Wypełnianie tabeli KRAJ popularnymi krajami teniśistów
    public function run(): void
    {
        $kraje = [
            // Europa
            ['Kod_ISO' => 'POL', 'Nazwa' => 'Polska',           'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'ESP', 'Nazwa' => 'Hiszpania',         'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'GBR', 'Nazwa' => 'Wielka Brytania',   'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'FRA', 'Nazwa' => 'Francja',           'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'GER', 'Nazwa' => 'Niemcy',            'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'ITA', 'Nazwa' => 'Włochy',            'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'SUI', 'Nazwa' => 'Szwajcaria',        'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'SRB', 'Nazwa' => 'Serbia',            'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'CZE', 'Nazwa' => 'Czechy',            'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'SVK', 'Nazwa' => 'Słowacja',          'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'HUN', 'Nazwa' => 'Węgry',             'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'AUT', 'Nazwa' => 'Austria',           'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'BEL', 'Nazwa' => 'Belgia',            'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'NED', 'Nazwa' => 'Holandia',          'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'SWE', 'Nazwa' => 'Szwecja',           'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'DEN', 'Nazwa' => 'Dania',             'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'NOR', 'Nazwa' => 'Norwegia',          'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'FIN', 'Nazwa' => 'Finlandia',         'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'RUS', 'Nazwa' => 'Rosja',             'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'UKR', 'Nazwa' => 'Ukraina',           'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'ROU', 'Nazwa' => 'Rumunia',           'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'CRO', 'Nazwa' => 'Chorwacja',         'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'GRE', 'Nazwa' => 'Grecja',            'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'BUL', 'Nazwa' => 'Bułgaria',          'Kontynent' => 'Europa'],
            ['Kod_ISO' => 'POR', 'Nazwa' => 'Portugalia',        'Kontynent' => 'Europa'],
            // Ameryka Północna
            ['Kod_ISO' => 'USA', 'Nazwa' => 'Stany Zjednoczone', 'Kontynent' => 'Ameryka Północna'],
            ['Kod_ISO' => 'CAN', 'Nazwa' => 'Kanada',            'Kontynent' => 'Ameryka Północna'],
            ['Kod_ISO' => 'MEX', 'Nazwa' => 'Meksyk',            'Kontynent' => 'Ameryka Północna'],
            // Ameryka Południowa
            ['Kod_ISO' => 'ARG', 'Nazwa' => 'Argentyna',         'Kontynent' => 'Ameryka Południowa'],
            ['Kod_ISO' => 'BRA', 'Nazwa' => 'Brazylia',          'Kontynent' => 'Ameryka Południowa'],
            ['Kod_ISO' => 'COL', 'Nazwa' => 'Kolumbia',          'Kontynent' => 'Ameryka Południowa'],
            ['Kod_ISO' => 'CHI', 'Nazwa' => 'Chile',             'Kontynent' => 'Ameryka Południowa'],
            // Azja
            ['Kod_ISO' => 'JPN', 'Nazwa' => 'Japonia',           'Kontynent' => 'Azja'],
            ['Kod_ISO' => 'CHN', 'Nazwa' => 'Chiny',             'Kontynent' => 'Azja'],
            ['Kod_ISO' => 'KOR', 'Nazwa' => 'Korea Południowa',  'Kontynent' => 'Azja'],
            ['Kod_ISO' => 'IND', 'Nazwa' => 'Indie',             'Kontynent' => 'Azja'],
            ['Kod_ISO' => 'KAZ', 'Nazwa' => 'Kazachstan',        'Kontynent' => 'Azja'],
            ['Kod_ISO' => 'THA', 'Nazwa' => 'Tajlandia',         'Kontynent' => 'Azja'],
            ['Kod_ISO' => 'TPE', 'Nazwa' => 'Tajwan',            'Kontynent' => 'Azja'],
            // Australia i Oceania
            ['Kod_ISO' => 'AUS', 'Nazwa' => 'Australia',         'Kontynent' => 'Australia i Oceania'],
            ['Kod_ISO' => 'NZL', 'Nazwa' => 'Nowa Zelandia',     'Kontynent' => 'Australia i Oceania'],
            // Afryka
            ['Kod_ISO' => 'RSA', 'Nazwa' => 'Republika Pd. Afryki', 'Kontynent' => 'Afryka'],
            ['Kod_ISO' => 'MAR', 'Nazwa' => 'Maroko',            'Kontynent' => 'Afryka'],
            ['Kod_ISO' => 'TUN', 'Nazwa' => 'Tunezja',           'Kontynent' => 'Afryka'],
        ];

        // Ignorujemy duplikaty jeśli seeder był już uruchomiony
        DB::table('KRAJ')->insertOrIgnore($kraje);
    }
}
