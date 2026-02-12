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

type UserRow = {
    id: number;
    name: string;
    email: string;
    created_at?: string | null;
};

const props = defineProps<{
    users: {
        data: UserRow[];
        links?: any[];
        meta?: any;
    };
}>();

const q = ref('');

function formatDate(iso?: string | null) {
    if (!iso) return '—';
    return iso.slice(0, 10);
}

const filtered = computed(() => {
    const query = q.value.trim().toLowerCase();
    const rows = props.users?.data ?? [];
    if (!query) return rows;

    return rows.filter((u) => {
        const hay = `${u.id} ${u.name} ${u.email}`.toLowerCase();
        return hay.includes(query);
    });
});
</script>

<template>
    <div class="max-w-6xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">
                Gestão de Acessos • Utilizadores
            </h1>

            <div class="flex gap-2">
                <Link href="/access/users/create">
                    <Button>Criar utilizador</Button>
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
                    <Input v-model="q" placeholder="Pesquisar (nome, email…)" />
                </div>
            </CardHeader>

            <CardContent>
                <div class="overflow-hidden rounded-lg border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>#</TableHead>
                                <TableHead>Nome</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Criado</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <TableRow v-for="u in filtered" :key="u.id">
                                <TableCell>{{ u.id }}</TableCell>
                                <TableCell>{{ u.name }}</TableCell>
                                <TableCell>{{ u.email }}</TableCell>
                                <TableCell>{{
                                    formatDate(u.created_at)
                                }}</TableCell>
                                <TableCell class="text-right">
                                    <Link :href="`/access/users/${u.id}/edit`">
                                        <Button variant="outline" size="sm"
                                            >Editar</Button
                                        >
                                    </Link>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="filtered.length === 0">
                                <TableCell
                                    colspan="5"
                                    class="p-6 text-center text-muted-foreground"
                                >
                                    Sem utilizadores.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- paginação do backend -->
                <div
                    v-if="props.users?.links?.length"
                    class="mt-4 flex flex-wrap gap-2"
                >
                    <Link
                        v-for="l in props.users.links"
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
