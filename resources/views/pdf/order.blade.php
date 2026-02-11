<!doctype html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <title>Encomenda</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 6px;
        }

        .muted {
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        th {
            background: #f3f4f6;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .totals {
            width: 280px;
            margin-left: auto;
            margin-top: 14px;
        }
    </style>
</head>

<body>
    @php

        $company = $order->company;
        $companyLogoPath = $company?->settings()->where('key', 'logo')->value('value');
        
        $logoFile = null;
        if (!empty($companyLogoPath)) {
            $candidate = storage_path('app/private/' . $companyLogoPath);
            if (file_exists($candidate)) {
                $logoFile = $candidate;
            }
        }
    @endphp

    <table style="width:100%; border:none; margin-bottom:12px;">
        <tr>
            <td style="border:none; width:80px; vertical-align:top;">
                @if($logoFile)
                    <img src="{{ $logoFile }}" alt="Logo" style="width:70px; height:70px; object-fit:contain;" />
                @endif
            </td>
            <td style="border:none; vertical-align:top;">
                <div style="font-size:16px; font-weight:bold;">
                    {{ $company?->name ?? config('app.name') }}
                </div>
                <div class="muted">
                    {{ $company?->address ?? '' }}
                    @if($company?->postal_code || $company?->locality)
                        <br>{{ trim(($company?->postal_code ?? '') . ' ' . ($company?->locality ?? '')) }}
                    @endif
                    @if($company?->tax_number)
                        <br>NIF: {{ $company->tax_number }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <h1>
        Encomenda {{ $order->number ?? ('ENC-' . $order->id) }}
    </h1>

    <div class="muted">
        Cliente: {{ $order->entity?->name ?? '—' }} <br>
        Data:
        {{ $order->order_date
            ? \Carbon\Carbon::parse($order->order_date)->format('d/m/Y')
            : '—'
        }} <br>

        Estado:
        {{ $order->status === 'closed' ? 'Fechado' : 'Rascunho' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Descrição</th>
                <th class="right">Qtd</th>
                <th class="right">Preço</th>
                <th class="right">IVA %</th>
                <th class="right">Total</th>
            </tr>
        </thead>

        <tbody>

            @php
            $subtotal = 0;
            $taxTotal = 0;
            @endphp

            @foreach($order->items as $item)

            @php
            $qty = (float) $item->qty;
            $price = (float) $item->unit_price;

            $line = $qty * $price;

            $taxRate = (float) ($item->tax_rate ?? 0);
            $tax = $line * ($taxRate / 100);

            $subtotal += $line;
            $taxTotal += $tax;
            @endphp

            <tr>
                <td>{{ $item->description }}</td>

                <td class="right">
                    {{ number_format($qty, 2, ',', '.') }}
                </td>

                <td class="right">
                    {{ number_format($price, 2, ',', '.') }}
                </td>

                <td class="right">
                    {{ number_format($taxRate, 2, ',', '.') }}
                </td>

                <td class="right">
                    {{ number_format($line + $tax, 2, ',', '.') }}
                </td>
            </tr>

            @endforeach

        </tbody>
    </table>

    @php
    $total = $subtotal + $taxTotal;
    @endphp

    <table class="totals">
        <tr>
            <th>Subtotal</th>
            <td class="right">
                {{ number_format($subtotal, 2, ',', '.') }}
            </td>
        </tr>

        <tr>
            <th>IVA</th>
            <td class="right">
                {{ number_format($taxTotal, 2, ',', '.') }}
            </td>
        </tr>

        <tr>
            <th>Total</th>
            <td class="right">
                <strong>
                    {{ number_format($total, 2, ',', '.') }}
                </strong>
            </td>
        </tr>
    </table>

</body>

</html>