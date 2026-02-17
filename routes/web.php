<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Carbon\Carbon;
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
use App\Http\Controllers\TenantSwitchController;
use App\Http\Controllers\TenantPlanController;
use App\Support\PlanLimits;
use App\Support\TenantUsage;
use App\Models\TenantEvent;
use App\Support\TenantContext;


// Public

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// Logo da empresa (armazenado fora de public_html)
Route::get('/company/logo', [CompanyController::class, 'logo'])->name('company.logo');

//Dashboard (auth + verified)

Route::get('dashboard', function () {
    $tenant = TenantContext::get();

    $plan = $tenant?->plan ?? 'trial';
    $maxUsers = $tenant ? PlanLimits::maxUsers($tenant) : 0;
    $usedUsers = $tenant ? TenantUsage::usersCount($tenant) : 0;

    // ---------- Datas sempre em "dia inteiro" ----------
    $today = now()->startOfDay();

    // Trial
    $trialEndsAt = $tenant?->trial_ends_at ? Carbon::parse($tenant->trial_ends_at)->startOfDay() : null;
    $trialWarnDays = (int) config('plan_limits.trial_warn_days', 7);

    $trialDaysLeft = ($tenant && $plan === 'trial' && $trialEndsAt)
        ? (int) $today->diffInDays($trialEndsAt, false)
        : null;

    $trialWarning = ($tenant && $plan === 'trial' && $trialEndsAt)
        ? $trialDaysLeft <= $trialWarnDays
        : false;

    // Delivery deadline
    $deadline = Carbon::parse(config('plan_limits.delivery_deadline', '2026-02-18'))->startOfDay();
    $deadlineWarnDays = (int) config('plan_limits.delivery_warn_days', 5);
    $deliveryDaysLeft = (int) $today->diffInDays($deadline, false);

    // ---------- Últimos eventos do tenant ----------
    $tenantEvents = [];
    if ($tenant) {
        $tenantEvents = TenantEvent::query()
            ->where('tenant_id', $tenant->id)
            ->latest('id')
            ->take(10)
            ->get(['id', 'type', 'from', 'to', 'meta', 'created_at'])
            ->map(fn ($e) => [
                'id' => $e->id,
                'type' => $e->type,
                'from' => $e->from,
                'to' => $e->to,
                'meta' => $e->meta,
                'created_at' => optional($e->created_at)->toDateTimeString(),
            ])
            ->values()
            ->all();
    }

    return Inertia::render('Dashboard', [
        'tenantPlan' => [
            'plan' => $plan,
            'trial_ends_at' => $trialEndsAt?->toDateTimeString(),
            'users' => [
                'used' => $usedUsers,
                'max' => $maxUsers,
            ],
            'trial_warning' => $trialWarning,
            'trial_days_left' => $trialDaysLeft,
        ],
        'delivery' => [
            'deadline' => $deadline->toDateString(),
            'warn' => $deliveryDaysLeft <= $deadlineWarnDays,
            'days_left' => $deliveryDaysLeft,
        ],
        'tenantEvents' => $tenantEvents,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');


// App (auth + verified)

Route::middleware(['auth', 'verified'])->group(function () {

//Base (sem gating)

    // Entities
    Route::get('/entities', [EntityController::class, 'index'])->name('entities.index');
    Route::get('/entities/create', [EntityController::class, 'create'])->name('entities.create');
    Route::get('/entities/{entity}/edit', [EntityController::class, 'edit'])->name('entities.edit');

    Route::post('/entities', [EntityController::class, 'store'])->name('entities.store');
    Route::put('/entities/{entity}', [EntityController::class, 'update'])->name('entities.update');
    Route::delete('/entities/{entity}', [EntityController::class, 'destroy'])->name('entities.destroy');

    // Entity contacts
    Route::post('/entities/{entity}/contacts', [EntityContactController::class, 'store'])->name('entities.contacts.store');
    Route::delete('/entities/{entity}/contacts/{contact}', [EntityContactController::class, 'destroy'])->name('entities.contacts.destroy');

    // Config - Contact Roles
    Route::resource('config/contact-roles', ContactRoleController::class)->names('contact-roles');

    // Contacts
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');

    // Config - Tax Rates
    Route::resource('config/tax-rates', TaxRateController::class)
        ->only(['index', 'create', 'store', 'destroy'])
        ->names('tax-rates');

    // Config - Products
    Route::resource('config/products', ProductController::class)->names('products');

    // Config - Empresa (single record)
    Route::get('/config/company', [CompanyController::class, 'edit'])->name('config.company.edit');
    Route::put('/config/company', [CompanyController::class, 'update'])->name('config.company.update');

 //Proposals (feature gated)

    Route::middleware(['feature:proposals'])->group(function () {
        Route::get('/proposals', [ProposalController::class, 'index'])->name('proposals.index');
        Route::get('/proposals/create', [ProposalController::class, 'create'])->name('proposals.create');
        Route::post('/proposals', [ProposalController::class, 'store'])->name('proposals.store');
        Route::get('/proposals/{proposal}/edit', [ProposalController::class, 'edit'])->name('proposals.edit');
        Route::put('/proposals/{proposal}', [ProposalController::class, 'update'])->name('proposals.update');
        Route::delete('/proposals/{proposal}', [ProposalController::class, 'destroy'])->name('proposals.destroy');

        Route::get('/proposals/{proposal}', [ProposalController::class, 'show'])->name('proposals.show');

        // PDF + Converter em encomenda (também gated)
        Route::get('/proposals/{proposal}/pdf', [ProposalController::class, 'pdf'])->name('proposals.pdf');
        Route::post('/proposals/{proposal}/convert-to-order', [ProposalController::class, 'convertToOrder'])
            ->name('proposals.convertToOrder');
    });

//Orders (feature gated)

    Route::middleware(['feature:orders'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

        Route::post('/orders/{order}/convert-suppliers', [OrderController::class, 'convertToSupplierOrders'])
            ->name('orders.convert-suppliers');

        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/pdf', [OrderController::class, 'pdf'])->name('orders.pdf');
    });


//Supplier Orders (feature gated)
 
    Route::middleware(['feature:supplier_orders'])->prefix('supplier-orders')->group(function () {
        Route::get('/', [SupplierOrderController::class, 'index'])->name('supplier-orders.index');
        Route::get('/{supplierOrder}', [SupplierOrderController::class, 'show'])->name('supplier-orders.show');
        Route::get('/{supplierOrder}/pdf', [SupplierOrderController::class, 'pdf'])->name('supplier-orders.pdf');
    });


//Finance - Supplier Invoices (feature gated)
 
    Route::middleware(['feature:supplier_invoices'])->prefix('finance')->group(function () {

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


//Access Management (feature gated + permission gated)

    Route::middleware(['feature:access_management'])->prefix('access')->group(function () {

        // Users
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

        // Roles
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

//Logs (feature gated + permission gated)

    Route::get('/logs', [LogController::class, 'index'])
        ->middleware(['feature:logs', 'permission:logs.view'])
        ->name('logs.index');
 
//Calendar (feature gated + permission gated)

    Route::middleware(['feature:calendar'])->group(function () {

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

        // Calendar - Config (Tipos/Ações)
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
    });


//Onboarding
 
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

//Tenant Switch + Plan

    Route::post('/tenants/switch', [TenantSwitchController::class, 'store'])->name('tenants.switch');

    Route::put('/tenants/{tenant}/plan', [TenantPlanController::class, 'update'])
        ->name('tenants.plan.update');
});

require __DIR__ . '/settings.php';

