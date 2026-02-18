<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const props = defineProps<{
    events: Array<{
        id: number;
        type: string;
        from: string | null;
        to: string | null;
        meta: any;
        created_at: string;
    }>;
}>();

const formatMeta = (meta: any) => {
    if (!meta) return '-';
    try {
        return JSON.stringify(meta, null, 2);
    } catch {
        return meta;
    }
};
</script>

<template>
    <div class="max-w-6xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Histórico do Tenant</h1>

            <div class="flex gap-2">
                <Link href="/dashboard">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Últimos 50 Eventos</CardTitle>
            </CardHeader>

            <CardContent>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="p-2 text-left">Data</th>
                            <th class="p-2 text-left">Tipo</th>
                            <th class="p-2 text-left">De</th>
                            <th class="p-2 text-left">Para</th>
                            <th class="p-2 text-left">Meta</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="event in props.events"
                            :key="event.id"
                            class="border-b hover:bg-muted"
                        >
                            <td class="p-2 whitespace-nowrap">
                                {{ event.created_at }}
                            </td>

                            <td class="p-2 font-medium">
                                {{ event.type }}
                            </td>

                            <td class="p-2">
                                {{ event.from ?? '-' }}
                            </td>

                            <td class="p-2">
                                {{ event.to ?? '-' }}
                            </td>

                            <td class="p-2">
                                <pre
                                    class="overflow-x-auto rounded bg-muted p-2 text-xs"
                                    >{{ formatMeta(event.meta) }}
                                </pre>
                            </td>
                        </tr>

                        <tr v-if="!props.events.length">
                            <td
                                colspan="5"
                                class="p-6 text-center text-muted-foreground"
                            >
                                Sem eventos registados.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
