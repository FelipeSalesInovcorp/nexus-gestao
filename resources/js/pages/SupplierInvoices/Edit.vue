<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Supplier = { id: number; name: string; email?: string | null };
type SupplierOrder = {
    id: number;
    number?: string | null;
    date?: string | null;
    total?: number | string | null;
};

type Invoice = {
    id: number;
    supplier_id?: number | null;
    supplier_order_id?: number | null;
    number?: string | null;
    issue_date?: string | null;
    due_date?: string | null;
    total?: number | string | null;
    notes?: string | null;
};

const props = defineProps<{
    invoice: Invoice;
    suppliers: Supplier[];
    supplierOrders: SupplierOrder[];
}>();

const form = useForm({
    supplier_id: props.invoice.supplier_id ?? '',
    supplier_order_id: props.invoice.supplier_order_id ?? '',
    number: props.invoice.number ?? '',
    issue_date: props.invoice.issue_date ?? '',
    due_date: props.invoice.due_date ?? '',
    total: props.invoice.total ?? '',
    notes: props.invoice.notes ?? '',
    document: null as File | null,
});

function onFileChange(e: Event) {
    const input = e.target as HTMLInputElement;
    form.document = input.files?.[0] ?? null;
}

function submit() {
    form.post(`/finance/supplier-invoices/${props.invoice.id}`, {
        method: 'put',
        forceFormData: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="max-w-3xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Editar fatura fornecedor</h1>

            <Link :href="`/finance/supplier-invoices/${props.invoice.id}`">
                <Button variant="outline">Voltar</Button>
            </Link>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Dados</CardTitle>
            </CardHeader>

            <CardContent>
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label>Fornecedor</Label>
                            <select
                                v-model="form.supplier_id"
                                class="w-full rounded-md border bg-background p-2 text-sm"
                            >
                                <option value="">Selecione…</option>
                                <option
                                    v-for="s in props.suppliers"
                                    :key="s.id"
                                    :value="s.id"
                                >
                                    {{ s.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.supplier_id"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.supplier_id }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Encomenda fornecedor (opcional)</Label>
                            <select
                                v-model="form.supplier_order_id"
                                class="w-full rounded-md border bg-background p-2 text-sm"
                            >
                                <option value="">—</option>
                                <option
                                    v-for="o in props.supplierOrders"
                                    :key="o.id"
                                    :value="o.id"
                                >
                                    {{ o.number ?? '#' + o.id }} —
                                    {{ o.date ?? '' }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.supplier_order_id"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.supplier_order_id }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Nº</Label>
                            <Input v-model="form.number" />
                            <p
                                v-if="form.errors.number"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.number }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Data da fatura</Label>
                            <Input v-model="form.issue_date" type="date" />
                            <p
                                v-if="form.errors.issue_date"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.issue_date }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Data de vencimento</Label>
                            <Input v-model="form.due_date" type="date" />
                            <p
                                v-if="form.errors.due_date"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.due_date }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Total</Label>
                            <Input v-model="form.total" />
                            <p
                                v-if="form.errors.total"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.total }}
                            </p>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <Label>Notas (opcional)</Label>
                            <Input v-model="form.notes" />
                            <p
                                v-if="form.errors.notes"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.notes }}
                            </p>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <Label>Substituir documento (opcional)</Label>
                            <input type="file" @change="onFileChange" />
                            <p
                                v-if="form.errors.document"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.document }}
                            </p>
                        </div>
                    </div>

                    <Button type="submit" :disabled="form.processing"
                        >Guardar alterações</Button
                    >
                </form>
            </CardContent>
        </Card>
    </div>
</template>
