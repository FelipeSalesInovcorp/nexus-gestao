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
use App\Http\Controllers\SupplierInvoiceController;
use App\Http\Controllers\Access\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Config\CalendarEventTypeController;
use App\Http\Controllers\Config\CalendarEventActionController;
use App\Http\Controllers\OnboardingController;


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

    /*Config - Empresa
    Route::get('/config/company', [CompanyController::class, 'edit'])->name('config.company.edit');
    Route::put('/config/company', [CompanyController::class, 'update'])->name('config.company.update');*/

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

    // Finance - Supplier Invoices
    Route::prefix('finance')->group(function () {
        Route::get('/supplier-invoices', [SupplierInvoiceController::class, 'index'])
            ->name('supplier-invoices.index');

        Route::get('/supplier-invoices/create', [SupplierInvoiceController::class, 'create'])
            ->name('supplier-invoices.create');

        Route::post('/supplier-invoices', [SupplierInvoiceController::class, 'store'])
            ->name('supplier-invoices.store');

        Route::get('/supplier-invoices/{supplierInvoice}', [SupplierInvoiceController::class, 'show'])
            ->name('supplier-invoices.show');

        Route::get('/supplier-invoices/{supplierInvoice}/edit', [SupplierInvoiceController::class, 'edit'])
            ->name('supplier-invoices.edit');

        Route::put('/supplier-invoices/{supplierInvoice}', [SupplierInvoiceController::class, 'update'])
            ->name('supplier-invoices.update');

        // Endpoint 1: marcar como paga (SEM comprovativo / SEM email)
        Route::post('/supplier-invoices/{supplierInvoice}/mark-paid', [SupplierInvoiceController::class, 'markPaid'])
            ->name('supplier-invoices.markPaid');

        // Endpoint 2: marcar como paga COM comprovativo (+ email opcional)
        Route::post('/supplier-invoices/{supplierInvoice}/mark-paid-with-proof', [SupplierInvoiceController::class, 'markPaidWithProof'])
            ->name('supplier-invoices.markPaidWithProof');

        Route::get('/supplier-invoices/{supplierInvoice}/download', [SupplierInvoiceController::class, 'download'])
            ->name('supplier-invoices.download');

        Route::get('/supplier-invoices/{supplierInvoice}/download-proof', [SupplierInvoiceController::class, 'downloadProof'])
            ->name('supplier-invoices.downloadProof');
    });

    // Access Management
    Route::prefix('access')->group(function () {
        Route::get('/users', [\App\Http\Controllers\Access\UserController::class, 'index'])
            ->name('access.users.index')
            ->middleware('permission:access.users.view');

        Route::get('/users/create', [\App\Http\Controllers\Access\UserController::class, 'create'])
            ->name('access.users.create')
            ->middleware('permission:access.users.create');

        Route::post('/users', [\App\Http\Controllers\Access\UserController::class, 'store'])
            ->name('access.users.store')
            ->middleware('permission:access.users.create');

        Route::get('/users/{user}/edit', [\App\Http\Controllers\Access\UserController::class, 'edit'])
            ->name('access.users.edit')
            ->middleware('permission:access.users.update');

        Route::put('/users/{user}', [\App\Http\Controllers\Access\UserController::class, 'update'])
            ->name('access.users.update')
            ->middleware('permission:access.users.update');
    });

    // Access - Roles
    Route::prefix('access')->group(function () {
        Route::get('/roles', [\App\Http\Controllers\Access\RoleController::class, 'index'])
            ->name('access.roles.index')
            ->middleware('permission:access.roles.view');

        Route::get('/roles/create', [\App\Http\Controllers\Access\RoleController::class, 'create'])
            ->name('access.roles.create')
            ->middleware('permission:access.roles.create');

        Route::post('/roles', [\App\Http\Controllers\Access\RoleController::class, 'store'])
            ->name('access.roles.store')
            ->middleware('permission:access.roles.create');

        Route::get('/roles/{role}/edit', [\App\Http\Controllers\Access\RoleController::class, 'edit'])
            ->name('access.roles.edit')
            ->middleware('permission:access.roles.update');

        Route::put('/roles/{role}', [\App\Http\Controllers\Access\RoleController::class, 'update'])
            ->name('access.roles.update')
            ->middleware('permission:access.roles.update');
    });

    // Logs
    Route::get('/logs', [LogController::class, 'index'])
    ->middleware('permission:logs.view')
    ->name('logs.index');

     // Calendar
    Route::prefix('calendar')->group(function () {
        Route::get('/', [CalendarController::class, 'index'])
            ->name('calendar.index')
            ->middleware('permission:calendar.view');

        Route::get('/events', [CalendarController::class, 'events'])
            ->name('calendar.events')
            ->middleware('permission:calendar.view');

        Route::post('/events', [CalendarController::class, 'store'])
            ->name('calendar.events.store')
            ->middleware('permission:calendar.create');

        Route::put('/events/{event}', [CalendarController::class, 'update'])
            ->name('calendar.events.update')
            ->middleware('permission:calendar.update');

        Route::delete('/events/{event}', [CalendarController::class, 'destroy'])
            ->name('calendar.events.destroy')
            ->middleware('permission:calendar.delete');
    });

    // Calendar - Eventos
    Route::prefix('config/calendar')->group(function () {
        // Tipos
        Route::get('/types', [CalendarEventTypeController::class, 'index'])
            ->name('config.calendar.types.index')
            ->middleware('permission:calendar.types.view');

        Route::post('/types', [CalendarEventTypeController::class, 'store'])
            ->name('config.calendar.types.store')
            ->middleware('permission:calendar.types.create');

        Route::put('/types/{type}', [CalendarEventTypeController::class, 'update'])
            ->name('config.calendar.types.update')
            ->middleware('permission:calendar.types.update');

        Route::delete('/types/{type}', [CalendarEventTypeController::class, 'destroy'])
            ->name('config.calendar.types.destroy')
            ->middleware('permission:calendar.types.delete');

        // Ações
        Route::get('/actions', [CalendarEventActionController::class, 'index'])
            ->name('config.calendar.actions.index')
            ->middleware('permission:calendar.actions.view');

        Route::post('/actions', [CalendarEventActionController::class, 'store'])
            ->name('config.calendar.actions.store')
            ->middleware('permission:calendar.actions.create');

        Route::put('/actions/{action}', [CalendarEventActionController::class, 'update'])
            ->name('config.calendar.actions.update')
            ->middleware('permission:calendar.actions.update');

        Route::delete('/actions/{action}', [CalendarEventActionController::class, 'destroy'])
            ->name('config.calendar.actions.destroy')
            ->middleware('permission:calendar.actions.delete');
    });

    // Onboarding
    Route::middleware(['auth'])->group(function () {

        Route::get('/onboarding', [OnboardingController::class, 'index'])
            ->name('onboarding.index');

        Route::post('/onboarding', [OnboardingController::class, 'store'])
            ->name('onboarding.store');
    });
});




require __DIR__ . '/settings.php';

