<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

import { Badge } from '@/components/ui/badge';
//import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

type OrderRow = {
    id: number;
    number?: string | null;
    order_date?: string | null;
    status: 'draft' | 'closed';
    total: number | string;
    entity?: { id: number; name: string } | null;
};

const props = defineProps<{
    orders: {
        data: OrderRow[];
        links: any[];
    };
}>();

function fmtDate(v: string | null) {
    if (!v) return '—';
    return new Date(v).toLocaleDateString('pt-PT');
}
</script>

<template>
    <div class="space-y-4 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold tracking-tight">Encomendas</h1>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Lista</CardTitle>
            </CardHeader>

            <CardContent>
                <div class="overflow-hidden rounded-xl border">
                    <Table>
                        <TableHeader class="bg-gray-100 dark:bg-gray-800">
                            <TableRow>
                                <TableHead>Data</TableHead>
                                <TableHead>Número</TableHead>
                                <TableHead>Cliente</TableHead>
                                <TableHead class="text-right">Total</TableHead>
                                <TableHead>Estado</TableHead>
                                <TableHead class="text-right">Abrir</TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <TableRow
                                v-for="o in props.orders.data"
                                :key="o.id"
                            >
                                <TableCell>{{
                                    fmtDate(o.order_date)
                                }}</TableCell>

                                <TableCell class="font-medium">
                                    {{ o.number ?? 'ENC-' + o.id }}
                                </TableCell>

                                <TableCell>
                                    {{ o.entity?.name ?? '—' }}
                                </TableCell>

                                <TableCell class="text-right">
                                    {{ o.total ?? '0.00' }}
                                </TableCell>

                                <TableCell>
                                    <Badge
                                        :variant="
                                            o.status === 'closed'
                                                ? 'default'
                                                : 'secondary'
                                        "
                                    >
                                        {{
                                            o.status === 'closed'
                                                ? 'Fechado'
                                                : 'Rascunho'
                                        }}
                                    </Badge>
                                </TableCell>

                                <TableCell class="text-right">
                                    <Link
                                        :href="`/orders/${o.id}`"
                                        class="underline"
                                    >
                                        Abrir
                                    </Link>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="props.orders.data.length === 0">
                                <TableCell
                                    colspan="6"
                                    class="py-8 text-center text-muted-foreground"
                                >
                                    Nenhuma encomenda encontrada.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
