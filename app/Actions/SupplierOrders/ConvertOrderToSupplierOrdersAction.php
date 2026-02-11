<?php

namespace App\Actions\SupplierOrders;

use App\Models\Order;
use App\Models\SupplierOrder;
use Illuminate\Support\Facades\DB;

class ConvertOrderToSupplierOrdersAction
{
    public function execute(Order $order): void
    {
        // Evitar duplicações
        if ($order->supplierOrders()->exists()) {
            return;
        }

        // Carregar items + product (para description e (opcionalmente) fallback de custo)
        $order->load('items.product');

        $itemsBySupplier = $order->items
            ->filter(fn ($item) => !empty($item->supplier_id))
            ->groupBy('supplier_id');

        if ($itemsBySupplier->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($order, $itemsBySupplier) {
            foreach ($itemsBySupplier as $supplierId => $items) {

                $nextNumber = (SupplierOrder::where('supplier_id', $supplierId)->max('number') ?? 0) + 1;

                $supplierOrder = SupplierOrder::create([
                    'supplier_id' => $supplierId,
                    'order_id'    => $order->id,
                    'number'      => $nextNumber,
                    'date'        => now(),
                    'status'      => 'draft',
                    'total'       => 0,
                ]);

                $total = 0;

                foreach ($items as $item) {
                    // qty (não quantity)
                    $qty = (float) ($item->qty ?? 0);

                    // custo: prioriza cost_price; se for null, usa fallback (ou 0)
                    // Fallback aqui usa product->price (atenção: pode ser preço de venda)
                    $cost = $item->cost_price !== null
                        ? (float) $item->cost_price
                        : (float) ($item->product?->price ?? 0);

                    $supplierOrder->items()->create([
                        'product_id'  => $item->product_id,
                        'description' => $item->description ?? ($item->product?->name ?? 'Item'),
                        'quantity'    => $qty,
                        'cost_price'  => $cost,
                    ]);

                    $total += $qty * $cost;
                }

                $supplierOrder->update([
                    'total' => round($total, 2),
                ]);
            }
        });
    }
}
