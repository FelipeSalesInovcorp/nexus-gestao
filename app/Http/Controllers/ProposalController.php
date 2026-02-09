<?php

namespace App\Http\Controllers;

use App\Actions\Proposals\CreateProposalAction;
use App\Actions\Proposals\ListProposalsAction;
use App\Actions\Proposals\UpdateProposalAction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Entity;
use App\Models\Product;
use App\Models\Proposal;
use App\Models\TaxRate;
use App\Models\Order;
use App\Models\OrderItem;


class ProposalController extends Controller
{
    public function index(Request $request, ListProposalsAction $action)
    {
        $filters = [
            'search' => $request->query('search'),
            'status' => $request->query('status'),
        ];

        return Inertia::render('Proposals/Index', [
            'filters' => $filters,
            'proposals' => $action->execute($filters),
        ]);
    }

    public function create()
    {
        return Inertia::render('Proposals/Create', [
            'clients' => Entity::query()->where('is_client', true)->orderBy('name')->get(['id','name']),
        ]);
    }

    public function store(Request $request, CreateProposalAction $action)
    {
        $data = $request->validate([
            'proposal_date' => ['required', 'date'],
            'valid_until' => ['required', 'date'],
            'entity_id' => ['required', 'exists:entities,id'],
            'status' => ['required', 'in:draft,closed'],
            'notes' => ['nullable', 'string'],
        ]);

        $proposal = $action->execute($data);

        return redirect()->route('proposals.edit', $proposal);
    }

    public function edit(Proposal $proposal)
    {
        $proposal->load(['entity:id,name', 'items']);

        return Inertia::render('Proposals/Edit', [
            'proposal' => $proposal,
            'clients' => Entity::query()->where('is_client', true)->orderBy('name')->get(['id','name']),
            'suppliers' => Entity::query()->where('is_supplier', true)->orderBy('name')->get(['id','name']),
            'products' => Product::query()->orderBy('name')->get(['id','name','reference','price','tax_rate_id']),
            'taxRates' => TaxRate::query()->orderBy('name')->get(['id','name','rate']),
        ]);
    }

    public function update(Request $request, Proposal $proposal, UpdateProposalAction $action)
    {
        $data = $request->validate([
            'proposal_date' => ['required', 'date'],
            'valid_until' => ['required', 'date'],
            'entity_id' => ['required', 'exists:entities,id'],
            'status' => ['required', 'in:draft,closed'],
            'notes' => ['nullable', 'string'],

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

        $action->execute($proposal, $data);

        return redirect()->back();
    }

    // Soft delete
    public function destroy(Proposal $proposal)
    {
        $proposal->delete();
        return redirect()->route('proposals.index');
    }
    
    // Generate PDF
    public function pdf(Proposal $proposal)
    {
        $proposal->load([
            'entity',
            'items.product',
            'items.supplier',
            'items.taxRate',
        ]);

        $pdf = Pdf::loadView('pdf.proposal', [
            'proposal' => $proposal,
        ]);

        $filename = ($proposal->number ?: 'PROPOSTA-' . $proposal->id) . '.pdf';

        return $pdf->download($filename);
    }

    // Convert to Order
    public function convertToOrder(Proposal $proposal)
    {
        $proposal->load(['items']);

        // cria encomenda em rascunho
        $order = Order::create([
            'number' => null, // podes gerar depois
            'order_date' => now()->toDateString(),
            'status' => 'draft',
            'total' => $proposal->total ?? 0,
            'entity_id' => $proposal->entity_id,
            'proposal_id' => $proposal->id,

        ]);

        foreach ($proposal->items as $it) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $it->product_id,
                'supplier_id' => $it->supplier_id,
                'tax_rate_id' => $it->tax_rate_id,
                'description' => $it->description,
                'qty' => $it->qty,
                'unit_price' => $it->unit_price,
                'tax_rate' => $it->tax_rate,
                'cost_price' => $it->cost_price,
            ]);
        }

        // para demo: volta ao edit da proposta com mensagem
        /*return redirect()
            ->route('proposals.edit', $proposal)
            ->with('success', 'Proposta convertida em Encomenda (Rascunho).');*/

        // redireciona para a encomenda
        return redirect()->route('orders.show', $order);
    }
}

