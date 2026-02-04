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

type EntityRow = {
    id: number;
    number?: string | number | null;
    name: string;
    nif?: string | null;
    email?: string | null;
    is_client: boolean;
    is_supplier: boolean;
    active: boolean;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type EntitiesPagination = {
    data: EntityRow[];
    links: PaginationLink[];
};

const props = defineProps<{
    type: string | null;
    filters: { search?: string } | null;
    entities: EntitiesPagination;
    countries: Array<any>;
}>();

const search = ref(props.filters?.search ?? '');

function submitSearch() {
    router.get(
        '/entities',
        { type: props.type, search: search.value },
        { preserveState: true },
    );
}

function goType(t: string | null) {
    router.get(
        '/entities',
        { type: t, search: search.value },
        { preserveState: true },
    );
}
</script>

<template>
    <div class="space-y-4 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Entidades</h1>

            <div class="flex gap-2">
                <Link href="/entities/create">
                    <Button>Nova Entidade</Button>
                </Link>

                <Button variant="outline" @click="goType('client')"
                    >Clientes</Button
                >
                <Button variant="outline" @click="goType('supplier')"
                    >Fornecedores</Button
                >
                <Button variant="outline" @click="goType(null)">Todas</Button>
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
                        placeholder="Pesquisar por nome, NIF ou email"
                    />
                    <Button @click="submitSearch">Pesquisar</Button>
                </div>

                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nº</TableHead>
                                <TableHead>Nome</TableHead>
                                <TableHead>NIF</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Tipo</TableHead>
                                <TableHead>Ativo</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <TableRow
                                v-for="e in props.entities.data"
                                :key="e.id"
                            >
                                <TableCell>{{ e.number }}</TableCell>
                                <TableCell class="font-medium">{{
                                    e.name
                                }}</TableCell>
                                <TableCell>{{ e.nif }}</TableCell>
                                <TableCell>{{ e.email }}</TableCell>

                                <TableCell>
                                    <div class="flex gap-2">
                                        <Badge
                                            v-if="e.is_client"
                                            variant="secondary"
                                            >Cliente</Badge
                                        >
                                        <Badge
                                            v-if="e.is_supplier"
                                            variant="secondary"
                                            >Fornecedor</Badge
                                        >
                                    </div>
                                </TableCell>

                                <TableCell>
                                    <Badge
                                        :variant="
                                            e.active ? 'default' : 'secondary'
                                        "
                                    >
                                        {{ e.active ? 'Ativo' : 'Inativo' }}
                                    </Badge>
                                </TableCell>

                                <TableCell class="text-right">
                                    <Link
                                        :href="`/entities/${e.id}/edit`"
                                        class="underline"
                                    >
                                        Editar
                                    </Link>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="props.entities.data.length === 0">
                                <TableCell
                                    colspan="7"
                                    class="py-8 text-center text-muted-foreground"
                                >
                                    Nenhuma entidade encontrada.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <Button
                    v-for="link in props.entities.links"
                    :key="link.label"
                    variant="outline"
                    :disabled="!link.url"
                    @click="link.url && router.visit(link.url)"
                >
                    {{
                        link.label
                            .replace(/&laquo;|&raquo;/g, '')
                            .replace(/&lsaquo;|&rsaquo;/g, '')
                    }}
                </Button>
            </CardContent>
        </Card>
    </div>
</template>
