<?php

namespace App\Mail;

use App\Models\SupplierInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SupplierInvoicePaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SupplierInvoice $invoice) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Comprovativo de Pagamento - Fatura ' . ($this->invoice->number ?? $this->invoice->id),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.supplier-invoices.paid',
        );
    }

    public function attachments(): array
    {
        if (!$this->invoice->proof_path) {
            return [];
        }

        if (!Storage::disk('private')->exists($this->invoice->proof_path)) {
            return [];
        }

        return [
            Attachment::fromStorageDisk(
                'private',
                $this->invoice->proof_path
            )->as('comprovativo.pdf'),
        ];
    }
}
