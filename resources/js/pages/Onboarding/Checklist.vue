<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const props = defineProps<{
    tenant: { id: number; name: string; plan: string };
    progress: { done: number; total: number; percent: number };
    items: Array<{
        key: string;
        title: string;
        description: string;
        done: boolean;
        href: string;
    }>;
}>();
</script>

<template>
    <div class="max-w-6xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Onboarding Checklist</h1>
                <div class="text-sm text-muted-foreground">
                    {{ props.tenant.name }} · Plano: {{ props.tenant.plan }}
                </div>
            </div>

            <div class="flex gap-2">
                <Link href="/dashboard">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Progresso</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <div>
                        {{ props.progress.done }} /
                        {{ props.progress.total }} concluído
                    </div>
                    <div class="font-medium">{{ props.progress.percent }}%</div>
                </div>

                <div class="h-2 w-full rounded bg-muted">
                    <div
                        class="h-2 rounded bg-primary"
                        :style="{ width: `${props.progress.percent}%` }"
                    />
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Checklist</CardTitle>
            </CardHeader>

            <CardContent>
                <div class="space-y-3">
                    <div
                        v-for="item in props.items"
                        :key="item.key"
                        class="flex items-start justify-between gap-4 rounded-md border p-4 hover:bg-muted"
                    >
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex h-6 items-center rounded px-2 text-xs font-medium"
                                    :class="
                                        item.done
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-yellow-100 text-yellow-800'
                                    "
                                >
                                    {{ item.done ? 'Concluído' : 'Pendente' }}
                                </span>

                                <div class="font-medium">
                                    {{ item.title }}
                                </div>
                            </div>

                            <div class="mt-1 text-sm text-muted-foreground">
                                {{ item.description }}
                            </div>
                        </div>

                        <div class="shrink-0">
                            <Link :href="item.href">
                                <Button size="sm" variant="outline">
                                    Abrir
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
