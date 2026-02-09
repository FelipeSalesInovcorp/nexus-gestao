<?php

namespace App\Http\Controllers;

use App\Actions\Orders\CreateOrderAction;
use App\Actions\Orders\UpdateOrderAction;
use App\Models\Entity;
use App\Models\Order;
use App\Models\Product;
use App\Models\TaxRate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->with(['entity:id,name'])
            ->orderByDesc('id')
            ->paginate(10)
            ->through(fn ($o) => [
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

    public function create()
    {
        return Inertia::render('Orders/Create', $this->formDeps());
    }

    public function edit(Order $order)
    {
        $order->load(['entity:id,name', 'items']);

        return Inertia::render('Orders/Edit', array_merge(
            ['order' => $order],
            $this->formDeps()
        ));
    }

    public function store(Request $request, CreateOrderAction $action)
    {
        $data = $this->validatedData($request);

        $order = $action->execute($data);

        return redirect()->route('orders.show', $order);
    }

    public function update(Request $request, Order $order, UpdateOrderAction $action)
    {
        $data = $this->validatedData($request);

        $action->execute($order, $data);

        return redirect()->back();
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('orders.index');
    }

    public function pdf(Order $order)
    {
        $order->load(['entity', 'items']);

        $pdf = Pdf::loadView('pdf.order', [
            'order' => $order,
        ]);

        $filename = ($order->number ?: ('ENC-' . $order->id)) . '.pdf';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=\"{$filename}\"");
    }

    /**
     * Dependências do formulário (Create/Edit)
     */
    private function formDeps(): array
    {
        return [
            'clients' => Entity::query()
                ->where('is_client', true)
                ->orderBy('name')
                ->get(['id', 'name']),

            'suppliers' => Entity::query()
                ->where('is_supplier', true)
                ->orderBy('name')
                ->get(['id', 'name']),

            'products' => Product::query()
                ->orderBy('name')
                ->get(['id', 'name', 'reference', 'price', 'tax_rate_id']),

            'taxRates' => TaxRate::query()
                ->orderBy('name')
                ->get(['id', 'name', 'rate']),
        ];
    }

    /**
     * Validação centralizada (Create/Update)
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'order_date' => ['required', 'date'],
            'entity_id' => ['required', 'exists:entities,id'],
            'status' => ['required', 'in:draft,closed'],

            'items' => ['array'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.qty' => ['required'],
            'items.*.unit_price' => ['required'],
            'items.*.tax_rate_id' => ['nullable', 'exists:tax_rates,id'],
            'items.*.tax_rate' => ['required'],
            'items.*.supplier_id' => ['nullable', 'exists:entities,id'],
            'items.*.cost_price' => ['nullable'],
        ]);
    }
}
