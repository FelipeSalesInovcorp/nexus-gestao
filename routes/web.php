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
use App\Http\Controllers\Config\CompanyController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ProposalItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplierOrderController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// Logo da empresa (armazenado fora de public_html)
Route::get('/company/logo', [CompanyController::class, 'logo'])->name('company.logo');

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
    /*Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    });*/
    // contacts new
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');


    // Config - Tax Rates
    Route::resource('config/tax-rates', TaxRateController::class)
        ->only(['index', 'create', 'store', 'destroy'])
        ->names('tax-rates');

    // Config - Products
    Route::resource('config/products', ProductController::class)
    ->names('products');

    // Config - Empresa
    Route::get('/config/company', [CompanyController::class, 'edit'])->name('config.company.edit');
    Route::put('/config/company', [CompanyController::class, 'update'])->name('config.company.update');

    // Config - Empresa (single record)
    Route::get('/config/company', [CompanyController::class, 'edit'])->name('config.company.edit');
    Route::put('/config/company', [CompanyController::class, 'update'])->name('config.company.update');

    // Proposals
    Route::get('/proposals', [ProposalController::class, 'index'])->name('proposals.index');
    Route::get('/proposals/create', [ProposalController::class, 'create'])->name('proposals.create');
    Route::post('/proposals', [ProposalController::class, 'store'])->name('proposals.store');
    Route::get('/proposals/{proposal}/edit', [ProposalController::class, 'edit'])->name('proposals.edit');
    Route::put('/proposals/{proposal}', [ProposalController::class, 'update'])->name('proposals.update');
    Route::delete('/proposals/{proposal}', [ProposalController::class, 'destroy'])->name('proposals.destroy'); // Soft delete

    Route::get('/proposals/{proposal}', [ProposalController::class, 'show'])->name('proposals.show');

    // PDF
    Route::get('/proposals/{proposal}/pdf', [ProposalController::class, 'pdf'])->name('proposals.pdf');

    // Converter em encomenda (draft)
    Route::post('/proposals/{proposal}/convert-to-order', [ProposalController::class, 'convertToOrder'])
        ->name('proposals.convertToOrder');

    // orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    // orders - create/edit pages
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    Route::post('/orders/{order}/convert-suppliers', [OrderController::class, 'convertToSupplierOrders'])
    ->name('orders.convert-suppliers');

    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/pdf', [OrderController::class, 'pdf'])->name('orders.pdf');

    // Supplier Orders
    Route::prefix('supplier-orders')->group(function () {

        Route::get('/', [SupplierOrderController::class, 'index'])
            ->name('supplier-orders.index');

        Route::get('/{supplierOrder}', [SupplierOrderController::class, 'show'])
            ->name('supplier-orders.show');

        Route::get('/{supplierOrder}/pdf', [SupplierOrderController::class, 'pdf'])
            ->name('supplier-orders.pdf');
    });
    
});

require __DIR__ . '/settings.php';

