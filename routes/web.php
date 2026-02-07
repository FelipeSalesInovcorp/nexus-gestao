<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\EntityContactController;
use App\Http\Controllers\Config\ContactRoleController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Config\TaxRateController;
use App\Http\Controllers\Config\ProductController;


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

    // Config - Contact Roles (resource)
    Route::resource('config/contact-roles', ContactRoleController::class)
        ->names('contact-roles');

    // Contacts
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    });

    // Config - Tax Rates
    Route::resource('config/tax-rates', TaxRateController::class)
        ->only(['index', 'create', 'store', 'destroy'])
        ->names('tax-rates');

    // Config - Products
    Route::resource('config/products', ProductController::class)
    ->names('products');
   
});

require __DIR__ . '/settings.php';

