<?php

namespace App\Actions\SupplierInvoices;

use App\Models\SupplierInvoice;
use App\Models\SupplierOrder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateSupplierInvoiceAction
{
    public function execute(SupplierInvoice $invoice, array $data, ?UploadedFile $document): SupplierInvoice
    {
        if (!empty($data['supplier_order_id'])) {
            $belongs = SupplierOrder::query()
                ->whereKey($data['supplier_order_id'])
                ->where('supplier_id', $data['supplier_id'])
                ->exists();

            if (!$belongs) {
                throw new \InvalidArgumentException('A encomenda selecionada não pertence ao fornecedor escolhido.');
            }
        }

        if ($document) {
            if ($invoice->document_path) {
                Storage::disk('private')->delete($invoice->document_path);
            }
            $data['document_path'] = $document->store('supplier-invoices', 'private');
        }

        $invoice->update($data);

        return $invoice;
    }
}
