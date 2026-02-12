<?php

namespace App\Actions\SupplierInvoices;

use App\Models\SupplierInvoice;
use App\Models\SupplierOrder;
use Illuminate\Http\UploadedFile;

class CreateSupplierInvoiceAction
{
    public function execute(array $data, UploadedFile $document): SupplierInvoice
    {
        // garantir encomenda pertence ao fornecedor (regra de negócio)
        if (!empty($data['supplier_order_id'])) {
            $belongs = SupplierOrder::query()
                ->whereKey($data['supplier_order_id'])
                ->where('supplier_id', $data['supplier_id'])
                ->exists();

            if (!$belongs) {
                // Pode lançar ValidationException se preferir; aqui vai simples:
                throw new \InvalidArgumentException('A encomenda selecionada não pertence ao fornecedor escolhido.');
            }
        }

        $data['document_path'] = $document->store('supplier-invoices', 'private');
        $data['status'] = 'pending';

        return SupplierInvoice::create($data);
    }
}
