<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\OrderItem;

class UpdateOrderAction
{
    public function execute(Order $order, array $data): Order
    {
        $order->update([
            'order_date' => $data['order_date'],
            'entity_id' => $data['entity_id'],
            'status' => $data['status'],
        ]);

        $order->items()->delete();

        $subtotal = 0;
        $taxTotal = 0;

        foreach (($data['items'] ?? []) as $it) {
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

        $order->total = round($subtotal + $taxTotal, 2);
        $order->save();

        return $order;
    }
}
