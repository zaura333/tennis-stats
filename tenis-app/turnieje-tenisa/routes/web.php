<?php

use App\Http\Controllers\EdycjaController;
use App\Http\Controllers\MeczController;
use App\Http\Controllers\TurniejController;
use App\Http\Controllers\ZawodnikController;
use Illuminate\Support\Facades\Route;

// Strona główna – przekierowanie do listy turniejów
Route::get('/', fn () => redirect()->route('turnieje.index'));

// Turnieje
Route::get('/turnieje',           [TurniejController::class, 'index'])->name('turnieje.index');
Route::get('/turnieje/create',    [TurniejController::class, 'create'])->name('turnieje.create');
Route::post('/turnieje',          [TurniejController::class, 'store'])->name('turnieje.store');
Route::get('/turnieje/{turniej}', [TurniejController::class, 'show'])->name('turnieje.show');

// Edycje turniejów
Route::get('/turnieje/{turniej}/edycje/create', [EdycjaController::class, 'create'])->name('edycje.create');
Route::post('/edycje',                           [EdycjaController::class, 'store'])->name('edycje.store');
Route::get('/edycje/{edycja}',                   [EdycjaController::class, 'show'])->name('edycje.show');

// Zawodnicy
Route::get('/zawodnicy',               [ZawodnikController::class, 'index'])->name('zawodnicy.index');
Route::get('/zawodnicy/create',        [ZawodnikController::class, 'create'])->name('zawodnicy.create');
Route::post('/zawodnicy',              [ZawodnikController::class, 'store'])->name('zawodnicy.store');
Route::get('/zawodnicy/{zawodnik}',    [ZawodnikController::class, 'show'])->name('zawodnicy.show');

// Mecze
Route::get('/edycje/{edycja}/mecze/create', [MeczController::class, 'create'])->name('mecze.create');
Route::post('/mecze',                        [MeczController::class, 'store'])->name('mecze.store');
Route::get('/mecze/{mecz}',                  [MeczController::class, 'show'])->name('mecze.show');
