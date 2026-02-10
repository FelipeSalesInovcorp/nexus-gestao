<!doctype html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <title>Proposta</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 10px;
        }

        .muted {
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
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
    </style>
</head>

<body>
    @php
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

    <h1>Proposta {{ $proposal->number ?? ('#' . $proposal->id) }}</h1>

    <div class="muted">
        Cliente: {{ $proposal->entity?->name ?? '—' }}<br>
        Data: {{ optional($proposal->proposal_date)->format('d/m/Y') }}<br>
        Validade: {{ optional($proposal->valid_until)->format('d/m/Y') }}<br>
        Estado: {{ $proposal->status === 'closed' ? 'Fechado' : 'Rascunho' }}
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

            @foreach($proposal->items as $it)
            @php
            $qty = (float) $it->qty;
            $price = (float) $it->unit_price;
            $line = $qty * $price;
            $taxRate = (float) ($it->tax_rate ?? 0);
            $tax = $line * ($taxRate / 100);
            $subtotal += $line;
            $taxTotal += $tax;
            @endphp

            <tr>
                <td>{{ $it->description }}</td>
                <td class="right">{{ number_format($qty, 2, ',', '.') }}</td>
                <td class="right">{{ number_format($price, 2, ',', '.') }}</td>
                <td class="right">{{ number_format($taxRate, 2, ',', '.') }}</td>
                <td class="right">{{ number_format($line + $tax, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @php $total = $subtotal + $taxTotal; @endphp

    <table style="width: 280px; margin-left: auto; margin-top: 12px;">
        <tr>
            <th>Subtotal</th>
            <td class="right">{{ number_format($subtotal, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <th>IVA</th>
            <td class="right">{{ number_format($taxTotal, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total</th>
            <td class="right"><b>{{ number_format($total, 2, ',', '.') }}</b></td>
        </tr>
    </table>
</body>

</html>