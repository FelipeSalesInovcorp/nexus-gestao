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

type LogRow = {
    id: number;
    date: string; // YYYY-MM-DD
    time: string; // HH:mm:ss
    user?: string | null;
    menu?: string | null;
    action?: string | null;
    device?: string | null;
    ip?: string | null;
};

const props = defineProps<{
    logs: {
        data: LogRow[];
        links?: any[];
        meta?: any;
    };
}>();

const q = ref('');

const filtered = computed(() => {
    const query = q.value.trim().toLowerCase();
    const rows = props.logs?.data ?? [];
    if (!query) return rows;

    return rows.filter((l) => {
        const hay = [
            l.id?.toString(),
            l.date,
            l.time,
            l.user,
            l.menu,
            l.action,
            l.ip,
            l.device,
        ]
            .filter(Boolean)
            .join(' ')
            .toLowerCase();

        return hay.includes(query);
    });
});

function shortDevice(ua?: string | null) {
    if (!ua) return '—';
    // só encurta visualmente (mantém texto completo no title)
    return ua.length > 60 ? ua.slice(0, 60) + '…' : ua;
}
</script>

<template>
    <div class="max-w-6xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Logs do Sistema</h1>

            <div class="flex gap-2">
                <Link href="/dashboard">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </div>

        <Card>
            <CardHeader
                class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
            >
                <CardTitle>Registos</CardTitle>

                <div class="w-full md:max-w-sm">
                    <Input
                        v-model="q"
                        placeholder="Pesquisar (utilizador, menu, ação, IP…)"
                    />
                </div>
            </CardHeader>

            <CardContent>
                <div class="overflow-hidden rounded-lg border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Data</TableHead>
                                <TableHead>Hora</TableHead>
                                <TableHead>Utilizador</TableHead>
                                <TableHead>Menu</TableHead>
                                <TableHead>Acção</TableHead>
                                <TableHead>IP</TableHead>
                                <TableHead>Dispositivo</TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <TableRow v-for="log in filtered" :key="log.id">
                                <TableCell class="whitespace-nowrap">{{
                                    log.date
                                }}</TableCell>
                                <TableCell class="whitespace-nowrap">{{
                                    log.time
                                }}</TableCell>
                                <TableCell>{{ log.user ?? '—' }}</TableCell>
                                <TableCell>{{ log.menu ?? '—' }}</TableCell>
                                <TableCell>{{ log.action ?? '—' }}</TableCell>
                                <TableCell class="whitespace-nowrap">{{
                                    log.ip ?? '—'
                                }}</TableCell>
                                <TableCell
                                    class="max-w-xs truncate"
                                    :title="log.device ?? ''"
                                >
                                    {{ shortDevice(log.device) }}
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="filtered.length === 0">
                                <TableCell
                                    colspan="7"
                                    class="p-6 text-center text-muted-foreground"
                                >
                                    Sem registos.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- Paginação do backend (Inertia) -->
                <div
                    v-if="props.logs?.links?.length"
                    class="mt-4 flex flex-wrap gap-2"
                >
                    <!--<Link v-for="l in props.logs.links" :key="l.url ?? l.label" :href="l.url ?? '#'" preserve-scroll
                        :class="[
                            'rounded-md border px-3 py-1 text-sm',
                            l.active ? 'bg-muted font-medium' : 'hover:bg-muted/50',
                            !l.url ? 'pointer-events-none opacity-50' : '',
                        ]" v-html="l.label" />-->

                    <Link
                        v-for="l in props.logs.links"
                        :key="l.url ?? l.label"
                        :href="l.url ?? '#'"
                        preserve-scroll
                        :class="[
                            'rounded-md border px-3 py-1 text-sm',
                            l.active
                                ? 'bg-muted font-medium'
                                : 'hover:bg-muted/50',
                            !l.url ? 'pointer-events-none opacity-50' : '',
                        ]"
                    >
                        <span v-html="l.label"></span>
                    </Link>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
