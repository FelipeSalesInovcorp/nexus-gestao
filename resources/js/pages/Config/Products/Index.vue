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

type TaxRateRow = { id: number; name: string; rate: number };

type ProductRow = {
    id: number;
    sku?: string | null;
    name: string;
    price: string | number;
    active: boolean;
    tax_rate?: { id: number; name: string; rate: number } | null;
};

type PaginationLink = { url: string | null; label: string; active: boolean };

const props = defineProps<{
    filters: { search?: string | null; active?: string | number | null };
    products: { data: ProductRow[]; links: PaginationLink[] };
    taxRates: TaxRateRow[];
}>();

const search = ref(props.filters?.search ?? '');
const active = ref(props.filters?.active ?? '');

function applyFilters() {
    router.get(
        '/config/products',
        {
            search: search.value || null,
            active: active.value === '' ? null : active.value,
        },
        { preserveState: true, preserveScroll: true },
    );
}

function clearFilters() {
    search.value = '';
    active.value = '';
    applyFilters();
}

function removeProduct(id: number) {
    if (!confirm('Remover este artigo?')) return;
    router.delete(`/config/products/${id}`, { preserveScroll: true });
}
</script>

<template>
    <div class="space-y-4 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Artigos / Produtos
                </h1>
                <p class="text-sm text-muted-foreground">
                    Gestão de artigos com IVA associado.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <Link href="/config/products/create">
                    <Button>Novo Artigo</Button>
                </Link>

                <Link href="/dashboard">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </div>

        <Card>
            <CardHeader class="pb-2">
                <CardTitle class="text-base">Filtros</CardTitle>
            </CardHeader>

            <CardContent class="space-y-4">
                <div class="grid gap-3 md:grid-cols-3">
                    <div class="space-y-2">
                        <div class="text-sm font-medium">Pesquisa</div>
                        <Input
                            v-model="search"
                            placeholder="SKU ou Nome"
                            @keyup.enter="applyFilters"
                        />
                    </div>

                    <div class="space-y-2">
                        <div class="text-sm font-medium">Ativo</div>
                        <select
                            v-model="active"
                            class="w-full rounded-md border px-3 py-2"
                        >
                            <option value="">—</option>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <Button variant="outline" @click="applyFilters"
                            >Aplicar</Button
                        >
                        <Button variant="ghost" @click="clearFilters"
                            >Limpar</Button
                        >
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border shadow-sm">
                    <Table>
                        <TableHeader class="bg-gray-100 dark:bg-gray-800">
                            <TableRow>
                                <TableHead>SKU</TableHead>
                                <TableHead>Nome</TableHead>
                                <TableHead>IVA</TableHead>
                                <TableHead>Preço</TableHead>
                                <TableHead>Ativo</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <TableRow
                                v-for="p in props.products.data"
                                :key="p.id"
                            >
                                <TableCell>{{ p.sku ?? '—' }}</TableCell>

                                <TableCell class="font-medium">{{
                                    p.name
                                }}</TableCell>

                                <TableCell>
                                    <span v-if="p.tax_rate">
                                        {{ p.tax_rate.name }} ({{
                                            p.tax_rate.rate
                                        }}%)
                                    </span>
                                    <span v-else>—</span>
                                </TableCell>

                                <TableCell>{{ p.price }}</TableCell>

                                <TableCell>
                                    <Badge
                                        :variant="
                                            p.active ? 'default' : 'secondary'
                                        "
                                    >
                                        {{ p.active ? 'Ativo' : 'Inativo' }}
                                    </Badge>
                                </TableCell>

                                <TableCell class="space-x-3 text-right">
                                    <Link
                                        :href="`/config/products/${p.id}/edit`"
                                        class="underline"
                                    >
                                        Editar
                                    </Link>

                                    <button
                                        class="underline"
                                        @click="removeProduct(p.id)"
                                    >
                                        Apagar
                                    </button>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="props.products.data.length === 0">
                                <TableCell
                                    colspan="6"
                                    class="py-8 text-center text-muted-foreground"
                                >
                                    Nenhum artigo encontrado.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Button
                        v-for="link in props.products.links"
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
