<?php

namespace App\Actions\SupplierOrders;

use App\Models\SupplierOrder;

class ListSupplierOrdersAction
{
    public function execute()
    {
        return SupplierOrder::query()
            ->with('supplier')
            ->latest()
            ->paginate(10);
    }
}
