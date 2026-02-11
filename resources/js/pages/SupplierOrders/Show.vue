<script setup>
import { Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

const props = defineProps({
    order: Object,
})

function fmtDate(v) {
    if (!v) return '—'
    return new Date(v).toLocaleDateString('pt-PT')
}
</script>

<template>
    <div class="max-w-5xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">
                Encomenda Fornecedor {{ props.order.number }}
            </h1>

            <div class="flex items-center gap-2">
                <a :href="`/supplier-orders/${props.order.id}/pdf`" target="_blank" rel="noopener noreferrer">
                    <Button type="button" variant="outline">PDF</Button>
                </a>

                <Link href="/supplier-orders">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Dados</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2 text-sm">
                <div><b>Fornecedor:</b> {{ props.order.supplier?.name ?? '—' }}</div>
                <div><b>Data:</b> {{ fmtDate(props.order.date) }}</div>
                <div><b>Estado:</b> {{ props.order.status }}</div>
                <div><b>Total:</b> {{ props.order.total }}</div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Linhas</CardTitle>
            </CardHeader>

            <CardContent>
                <table class="w-full overflow-hidden rounded-lg border text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="p-2 text-left">Descrição</th>
                            <th class="p-2 text-right">Qtd</th>
                            <th class="p-2 text-right">Custo</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="item in props.order.items" :key="item.id" class="border-t">
                            <td class="p-2">{{ item.description }}</td>
                            <td class="p-2 text-right">{{ item.quantity }}</td>
                            <td class="p-2 text-right">{{ item.cost_price }}</td>
                        </tr>

                        <tr v-if="(props.order.items ?? []).length === 0">
                            <td colspan="3" class="p-4 text-center text-muted-foreground">
                                Sem linhas.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
