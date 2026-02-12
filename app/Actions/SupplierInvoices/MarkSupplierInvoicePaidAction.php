<?php

namespace App\Actions\SupplierInvoices;

use App\Mail\SupplierInvoicePaidMail;
use App\Models\SupplierInvoice;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class MarkSupplierInvoicePaidAction
{
    /**
     * Marca a fatura como paga, guarda comprovativo (proof_path) e, opcionalmente, envia email ao fornecedor.
     *
     * Atenção: os nomes dos parâmetros foram escolhidos para bater com a chamada no Controller:
     * execute(supplierInvoice: ..., proofFile: ..., sendEmail: ...)
     */
    public function execute(
        SupplierInvoice $supplierInvoice,
        UploadedFile $proofFile,
        bool $sendEmail = true
    ): SupplierInvoice {
        // remover comprovativo antigo (se existir)
        if (!empty($supplierInvoice->proof_path)) {
            Storage::disk('private')->delete($supplierInvoice->proof_path);
        }

        // guardar novo comprovativo
        $supplierInvoice->proof_path = $proofFile->store('supplier-invoices/proofs', 'private');

        // marcar como paga
        $supplierInvoice->status = 'paid';
        $supplierInvoice->paid_at = now();
        $supplierInvoice->save();

        // email opcional (briefing: apenas se utilizador confirmar no diálogo)
        if ($sendEmail) {
            $supplierInvoice->loadMissing('supplier');

            $to = $supplierInvoice->supplier?->email;

            if (!empty($to)) {
                Mail::to($to)->send(new SupplierInvoicePaidMail($supplierInvoice));
            }
        }

        return $supplierInvoice;
    }
}
