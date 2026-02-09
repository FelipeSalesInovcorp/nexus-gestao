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