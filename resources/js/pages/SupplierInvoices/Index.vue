<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type Invoice = {
    id: number;
    number?: string | null;
    supplier?: { name: string } | null;
    status?: string | null;
    total?: number | string | null;
    date?: string | null;
};

const props = defineProps<{
    invoices: {
        data: Invoice[];
        links?: any[];
        meta?: any;
    };
}>();

console.log(props.invoices.data[0]);
</script>

<template>
    <div class="max-w-6xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Faturas a fornecedores</h1>

            <div class="flex gap-2">
                <Link href="/finance/supplier-invoices/create">
                    <Button>Criar fatura</Button>
                </Link>
                <Link href="/dashboard">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Lista</CardTitle>
            </CardHeader>

            <CardContent>
                <table class="w-full overflow-hidden rounded-lg border text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="p-2 text-left">#</th>
                            <th class="p-2 text-left">Fornecedor</th>
                            <th class="p-2 text-left">Estado</th>
                            <th class="p-2 text-right">Total</th>
                            <th class="p-2 text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="inv in props.invoices?.data ?? []"
                            :key="inv.id"
                            class="border-t"
                        >
                            <td class="p-2">
                                <Link
                                    class="underline"
                                    :href="`/finance/supplier-invoices/${inv.id}`"
                                >
                                    {{ inv.number ?? inv.id }}
                                </Link>
                            </td>
                            <td class="p-2">{{ inv.supplier?.name ?? '—' }}</td>
                            <td class="p-2">{{ inv.status ?? '—' }}</td>
                            <td class="p-2 text-right">
                                {{ inv.total ?? '—' }}
                            </td>
                            <td class="p-2 text-right">
                                <Link
                                    :href="`/finance/supplier-invoices/${inv.id}/edit`"
                                >
                                    <Button variant="outline" size="sm"
                                        >Editar</Button
                                    >
                                </Link>
                            </td>
                        </tr>

                        <tr v-if="(props.invoices?.data ?? []).length === 0">
                            <td
                                colspan="5"
                                class="p-4 text-center text-muted-foreground"
                            >
                                Sem faturas.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
