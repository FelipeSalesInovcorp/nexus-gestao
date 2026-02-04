<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\EntityController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/entities', [EntityController::class, 'index'])->name('entities.index');
    Route::post('/entities', [EntityController::class, 'store'])->name('entities.store');
    Route::put('/entities/{entity}', [EntityController::class, 'update'])->name('entities.update');
    Route::delete('/entities/{entity}', [EntityController::class, 'destroy'])->name('entities.destroy');
});

require __DIR__.'/settings.php';
