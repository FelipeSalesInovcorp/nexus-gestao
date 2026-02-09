<script setup lang="ts">
import { router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableHeader,
    TableBody,
    TableRow,
    TableHead,
    TableCell,
} from '@/components/ui/table';

type PaginationLink = { url: string | null; label: string; active: boolean };
type TaxRateRow = { id: number; name: string; rate: string; active: boolean };

const props = defineProps<{
    filters: { search?: string | null };
    taxRates: { data: TaxRateRow[]; links: PaginationLink[] };
}>();

const search = ref(props.filters?.search ?? '');

function apply() {
    router.get(
        '/config/tax-rates',
        { search: search.value || null },
        { preserveState: true, preserveScroll: true },
    );
}

function remove(id: number) {
    if (!confirm('Apagar esta taxa de IVA?')) return;
    router.delete(`/config/tax-rates/${id}`, { preserveScroll: true });
}
</script>

<template>
    <div class="space-y-4 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold tracking-tight">IVA</h1>

            <div class="flex items-center gap-2">
                <Link href="/config/tax-rates/create">
                    <Button>Nova taxa</Button>
                </Link>
                <Link href="/dashboard">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </div>

        <Card>
            <CardHeader class="pb-2">
                <CardTitle class="text-base">Pesquisa</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="flex gap-2">
                    <Input
                        v-model="search"
                        placeholder="Pesquisar por nome"
                        @keyup.enter="apply"
                    />
                    <Button variant="outline" @click="apply">Pesquisar</Button>
                </div>

                <div class="overflow-hidden rounded-xl border shadow-sm">
                    <Table>
                        <TableHeader class="bg-gray-100 dark:bg-gray-800">
                            <TableRow>
                                <TableHead>Nome</TableHead>
                                <TableHead>Taxa</TableHead>
                                <TableHead>Estado</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <TableRow
                                v-for="t in props.taxRates.data"
                                :key="t.id"
                            >
                                <TableCell class="font-medium">{{
                                    t.name
                                }}</TableCell>
                                <TableCell>{{ t.rate }}%</TableCell>
                                <TableCell>
                                    <Badge
                                        :variant="
                                            t.active ? 'default' : 'secondary'
                                        "
                                    >
                                        {{ t.active ? 'Ativo' : 'Inativo' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button
                                        variant="outline"
                                        @click="remove(t.id)"
                                        >Apagar</Button
                                    >
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="props.taxRates.data.length === 0">
                                <TableCell
                                    colspan="4"
                                    class="py-8 text-center text-muted-foreground"
                                >
                                    Nenhuma taxa encontrada.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Button
                        v-for="link in props.taxRates.links"
                        :key="link.label"
                        variant="outline"
                        :disabled="!link.url"
                        @click="link.url && router.visit(link.url)"
                    >
                        {{
                            link.label.replace(
                                /&laquo;|&raquo;|&lsaquo;|&rsaquo;/g,
                                '',
                            )
                        }}
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
