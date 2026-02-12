<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

type RoleRow = { id: number; name: string };

const props = defineProps<{
    roles: {
        data: RoleRow[];
        links?: any[];
    };
}>();

const q = ref('');

const filtered = computed(() => {
    const query = q.value.trim().toLowerCase();
    const rows = props.roles?.data ?? [];
    if (!query) return rows;
    return rows.filter((r) =>
        `${r.id} ${r.name}`.toLowerCase().includes(query),
    );
});
</script>

<template>
    <div class="max-w-6xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Gestão de Acessos • Roles</h1>

            <div class="flex gap-2">
                <Link href="/access/roles/create">
                    <Button>Criar role</Button>
                </Link>
                <Link href="/access/users">
                    <Button variant="outline">Utilizadores</Button>
                </Link>
                <Link href="/dashboard">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </div>

        <Card>
            <CardHeader
                class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
            >
                <CardTitle>Lista</CardTitle>
                <div class="w-full md:max-w-sm">
                    <Input v-model="q" placeholder="Pesquisar (nome…)" />
                </div>
            </CardHeader>

            <CardContent>
                <div class="overflow-hidden rounded-lg border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>#</TableHead>
                                <TableHead>Nome</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <TableRow v-for="r in filtered" :key="r.id">
                                <TableCell>{{ r.id }}</TableCell>
                                <TableCell>{{ r.name }}</TableCell>
                                <TableCell class="text-right">
                                    <Link :href="`/access/roles/${r.id}/edit`">
                                        <Button variant="outline" size="sm"
                                            >Editar</Button
                                        >
                                    </Link>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="filtered.length === 0">
                                <TableCell
                                    colspan="3"
                                    class="p-6 text-center text-muted-foreground"
                                >
                                    Sem roles.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- paginação -->
                <div
                    v-if="props.roles?.links?.length"
                    class="mt-4 flex flex-wrap gap-2"
                >
                    <Link
                        v-for="l in props.roles.links"
                        :key="l.url ?? l.label"
                        :href="l.url ?? '#'"
                        preserve-scroll
                        :class="[
                            'rounded-md border px-3 py-1 text-sm',
                            l.active
                                ? 'bg-muted font-medium'
                                : 'hover:bg-muted/50',
                            !l.url ? 'pointer-events-none opacity-50' : '',
                            // eslint-disable-next-line vue/no-v-text-v-html-on-component
                        ]"
                        v-html="l.label"
                    />
                </div>
            </CardContent>
        </Card>
    </div>
</template>
