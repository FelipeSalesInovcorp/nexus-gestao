<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\EntityContactController;
use App\Http\Controllers\ContactRoleController;

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

    // Entities - create/edit pages
    Route::get('/entities/create', [EntityController::class, 'create'])->name('entities.create');
    Route::get('/entities/{entity}/edit', [EntityController::class, 'edit'])->name('entities.edit');

    Route::post('/entities', [EntityController::class, 'store'])->name('entities.store');
    Route::put('/entities/{entity}', [EntityController::class, 'update'])->name('entities.update');
    Route::delete('/entities/{entity}', [EntityController::class, 'destroy'])->name('entities.destroy');

    // Entity contacts
    Route::post('/entities/{entity}/contacts', [EntityContactController::class, 'store'])->name('entities.contacts.store');
    Route::delete('/entities/{entity}/contacts/{contact}', [EntityContactController::class, 'destroy'])->name('entities.contacts.destroy');

    // Config - Contact Roles
    Route::prefix('config')->group(function () {
        Route::get('/contact-roles', [ContactRoleController::class, 'index'])->name('contact-roles.index');
        Route::get('/contact-roles/create', [ContactRoleController::class, 'create'])->name('contact-roles.create');
        Route::post('/contact-roles', [ContactRoleController::class, 'store'])->name('contact-roles.store');
        Route::get('/contact-roles/{role}/edit', [ContactRoleController::class, 'edit'])->name('contact-roles.edit');
        Route::put('/contact-roles/{role}', [ContactRoleController::class, 'update'])->name('contact-roles.update');
        Route::delete('/contact-roles/{role}', [ContactRoleController::class, 'destroy'])->name('contact-roles.destroy');
    });
});

require __DIR__ . '/settings.php';

