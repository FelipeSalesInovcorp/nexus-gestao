<?php

namespace App\Http\Controllers;

use App\Actions\SupplierInvoices\CreateSupplierInvoiceAction;
use App\Actions\SupplierInvoices\MarkSupplierInvoicePaidAction;
use App\Actions\SupplierInvoices\MarkSupplierInvoicePaidWithoutProofAction;
use App\Actions\SupplierInvoices\UpdateSupplierInvoiceAction;
use App\Http\Requests\SupplierInvoices\StoreSupplierInvoiceRequest;
use App\Http\Requests\SupplierInvoices\UpdateSupplierInvoiceRequest;
use App\Http\Requests\SupplierInvoices\MarkPaidWithProofRequest;
use App\Models\Entity;
use App\Models\SupplierInvoice;
use App\Models\SupplierOrder;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SupplierInvoiceController extends Controller
{
    public function index()
    {
        return Inertia::render('SupplierInvoices/Index', [
            'invoices' => SupplierInvoice::query()
                ->with(['supplier', 'supplierOrder'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create()
    {
        return Inertia::render('SupplierInvoices/Create', [
            'suppliers' => Entity::query()
                ->where('is_supplier', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
            'supplierOrders' => [],
        ]);
    }

    public function store(
        StoreSupplierInvoiceRequest $request,
        CreateSupplierInvoiceAction $action
    ) {
        $invoice = $action->execute(
            $request->validated(),
            $request->file('document')
        );

        return redirect()->route('supplier-invoices.show', $invoice);
    }

    public function show(SupplierInvoice $supplierInvoice)
    {
        $supplierInvoice->load(['supplier', 'supplierOrder']);

        return Inertia::render('SupplierInvoices/Show', [
            //'invoice' => $supplierInvoice,
            'invoice' => array_merge($supplierInvoice->toArray(), [
                'issue_date' => optional($supplierInvoice->issue_date)->format('Y-m-d'),
                'due_date'   => optional($supplierInvoice->due_date)->format('Y-m-d'),
                'paid_at'    => optional($supplierInvoice->paid_at)->format('Y-m-d H:i'),
            ]),
        ]);
    }

    public function edit(SupplierInvoice $supplierInvoice)
    {
        $supplierInvoice->load(['supplier', 'supplierOrder']);

        return Inertia::render('SupplierInvoices/Edit', [
            'invoice' => $supplierInvoice,
            'suppliers' => Entity::query()
                ->where('is_supplier', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
            'supplierOrders' => SupplierOrder::query()
                ->where('supplier_id', $supplierInvoice->supplier_id)
                ->orderByDesc('date')
                ->get(['id', 'number', 'date', 'total']),
        ]);
    }

    public function update(
        UpdateSupplierInvoiceRequest $request,
        SupplierInvoice $supplierInvoice,
        UpdateSupplierInvoiceAction $action
    ) {
        $action->execute(
            $supplierInvoice,
            $request->validated(),
            $request->file('document')
        );

        return redirect()->route('supplier-invoices.show', $supplierInvoice);
    }

    // Endpoint 1: paga sem comprovativo
    public function markPaid(
        SupplierInvoice $supplierInvoice,
        MarkSupplierInvoicePaidWithoutProofAction $action
    ) {
        $action->execute($supplierInvoice);

        return back();
    }

    // Endpoint 2: paga com comprovativo (+ email opcional)
    public function markPaidWithProof(
        MarkPaidWithProofRequest $request,
        SupplierInvoice $supplierInvoice,
        MarkSupplierInvoicePaidAction $action
    ) {
        $action->execute(
            supplierInvoice: $supplierInvoice,
            proofFile: $request->file('proof'),
            sendEmail: $request->boolean('send_email', true),
        );

        return back();
    }

    public function download(SupplierInvoice $supplierInvoice)
    {
        abort_if(empty($supplierInvoice->document_path), 404);
        return Storage::disk('private')->download($supplierInvoice->document_path);
    }

    public function downloadProof(SupplierInvoice $supplierInvoice)
    {
        abort_if(empty($supplierInvoice->proof_path), 404);
        return Storage::disk('private')->download($supplierInvoice->proof_path);
    }
}
