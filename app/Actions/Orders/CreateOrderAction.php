<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\OrderItem;

class CreateOrderAction
{
    public function execute(array $data): Order
    {
        $order = Order::create([
            'number' => null,
            'order_date' => $data['order_date'],
            'status' => $data['status'],
            'total' => 0,
            'entity_id' => $data['entity_id'],
            'proposal_id' => null,
        ]);

        // numeração automática
        $order->number = 'ENC-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT);
        $order->save();

        [$subtotal, $taxTotal] = $this->syncItemsAndTotals($order, $data['items'] ?? []);

        $order->total = round($subtotal + $taxTotal, 2);
        $order->save();

        return $order;
    }

    private function syncItemsAndTotals(Order $order, array $items): array
    {
        $subtotal = 0;
        $taxTotal = 0;

        foreach ($items as $it) {
            $qty = (float) $it['qty'];
            $price = (float) $it['unit_price'];
            $line = $qty * $price;

            $taxRate = (float) $it['tax_rate'];
            $tax = $line * ($taxRate / 100);

            $subtotal += $line;
            $taxTotal += $tax;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $it['product_id'] ?? null,
                'supplier_id' => $it['supplier_id'] ?? null,
                'tax_rate_id' => $it['tax_rate_id'] ?? null,
                'description' => $it['description'],
                'qty' => $it['qty'],
                'unit_price' => $it['unit_price'],
                'tax_rate' => $it['tax_rate'],
                'cost_price' => $it['cost_price'] ?? null,
            ]);
        }

        return [$subtotal, $taxTotal];
    }
}
