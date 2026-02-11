<?php

namespace App\Http\Controllers;

use App\Actions\SupplierOrders\ListSupplierOrdersAction;
use App\Models\SupplierOrder;
use App\Models\CompanySetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class SupplierOrderController extends Controller
{
    public function index(ListSupplierOrdersAction $list)
    {
        return Inertia::render('SupplierOrders/Index', [
            'orders' => $list->execute(),
        ]);
    }

    public function show(SupplierOrder $supplierOrder)
    {
        $supplierOrder->load('supplier', 'items'); // <- aqui

        return Inertia::render('SupplierOrders/Show', [
            'order' => $supplierOrder,
        ]);
    }

    public function pdf(SupplierOrder $supplierOrder)
    {
        $supplierOrder->load('supplier', 'items'); // <- aqui

        $company = CompanySetting::first();

        $pdf = Pdf::loadView('pdf.supplier-order', [
            'order' => $supplierOrder,
            'company' => $company,
        ]);

        return $pdf->stream("encomenda-fornecedor-{$supplierOrder->number}.pdf");
    }
}
