@component('mail::message')
Exmos. Senhores,

Enviamos em anexo o comprovativo de pagamento referente à fatura **{{ $invoice->number ?? $invoice->id }}**.

Com os melhores cumprimentos,  
**{{ config('app.name') }}**
@endcomponent
