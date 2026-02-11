<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

const props = defineProps({
    orders: {
        type: Object,
        default: () => ({ data: [] }),
    },
})
</script>

<template>
    <div class="p-6 space-y-6 max-w-6xl">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">
                Encomendas Fornecedor
            </h1>

            <div class="flex gap-2">
                <!--<Link href="/orders/create">
                <Button>Nova Encomenda</Button>
            </Link>-->

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
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="p-2 text-left">Número</th>
                            <th class="p-2 text-left">Fornecedor</th>
                            <th class="p-2">Estado</th>
                            <th class="p-2 text-right">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="order in props.orders.data"
                            :key="order.id"
                            class="border-b hover:bg-muted"
                        >
                            <td class="p-2">
                                <Link :href="`/supplier-orders/${order.id}`" class="text-primary underline">
                                    {{ order.number }}
                                </Link>
                            </td>

                            <td class="p-2">
                                {{ order.supplier?.name }}
                            </td>

                            <td class="p-2 text-center">
                                {{ order.status }}
                            </td>

                            <td class="p-2 text-right">
                                {{ order.total }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>

