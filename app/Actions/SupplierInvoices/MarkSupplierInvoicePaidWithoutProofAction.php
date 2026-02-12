<?php

namespace App\Actions\SupplierInvoices;

use App\Models\SupplierInvoice;

class MarkSupplierInvoicePaidWithoutProofAction
{
    public function execute(SupplierInvoice $supplierInvoice): SupplierInvoice
    {
        $supplierInvoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return $supplierInvoice;
    }
}
