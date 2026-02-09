<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Inertia\Inertia;

class OrderController extends Controller
{
    //

    public function index()
    {
        $orders = Order::query()
            ->with(['entity:id,name'])
            ->orderByDesc('id')
            ->paginate(10)
            ->through(fn($o) => [
                'id' => $o->id,
                'number' => $o->number,
                'order_date' => $o->order_date,
                'status' => $o->status,
                'total' => $o->total,
                'entity' => $o->entity ? ['id' => $o->entity->id, 'name' => $o->entity->name] : null,
                'proposal_id' => $o->proposal_id,
            ]);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['entity:id,name', 'items']);
        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }
}
