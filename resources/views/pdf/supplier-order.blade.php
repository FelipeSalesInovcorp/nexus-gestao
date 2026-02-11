<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Encomenda a Fornecedor</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .muted { color: #666; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px; border-bottom: 1px solid #ddd; }
        th { text-align: left; background: #f5f5f5; }
        .right { text-align: right; }
        .top { vertical-align: top; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td class="top">
                <div style="font-size:16px; font-weight:bold;">
                    {{ $company?->name ?? config('app.name') }}
                </div>
                <div class="muted">
                    {{ $company?->address ?? '' }}
                    @if(($company?->postal_code ?? null) || ($company?->locality ?? null))
                        <br>{{ trim(($company?->postal_code ?? '') . ' ' . ($company?->locality ?? '')) }}
                    @endif
                    @if($company?->tax_number)
                        <br>NIF: {{ $company->tax_number }}
                    @endif
                </div>
            </td>

            <td class="top right" style="width: 40%;">
                <div style="font-size:16px; font-weight:bold;">Encomenda a Fornecedor</div>
                <div class="muted">
                    Nº: {{ $order->number ?? $order->id }}<br>
                    Data: {{ optional($order->date)->format('d/m/Y') ?? (is_string($order->date) ? \Carbon\Carbon::parse($order->date)->format('d/m/Y') : '') }}<br>
                    Estado: {{ $order->status }}
                </div>
            </td>
        </tr>
    </table>

    <hr style="margin: 12px 0;">

    <div style="margin-bottom: 10px;">
        <b>Fornecedor:</b> {{ $order->supplier?->name ?? '—' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Descrição</th>
                <th class="right">Qtd</th>
                <th class="right">Custo</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->items as $item)
                @php
                    $qty = (float) $item->quantity;
                    $cost = (float) $item->cost_price;
                    $subtotal = $qty * $cost;
                @endphp
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="right">{{ number_format($qty, 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($cost, 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($subtotal, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="muted">Sem linhas.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="right"><b>Total</b></td>
                <td class="right"><b>{{ number_format((float)$order->total, 2, ',', '.') }}</b></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
