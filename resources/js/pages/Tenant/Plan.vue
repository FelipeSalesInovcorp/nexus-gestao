<script setup lang="ts">
import { router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const props = defineProps<{
    tenant: {
        id: number;
        plan: string;
        scheduled_plan: string | null;
        scheduled_plan_at: string | null;
    };
}>();

const selectedPlan = ref(props.tenant.plan);

const updatePlan = () => {
    router.put(`/tenants/${props.tenant.id}/plan`, {
        plan: selectedPlan.value,
    });
};

const cancelSubscription = () => {
    if (
        !confirm(
            'Tens a certeza que queres cancelar? O plano mantém-se até ao fim do ciclo.',
        )
    )
        return;

    router.post('/tenant/cancel');
};
</script>

<template>
    <div class="max-w-4xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Gestão de Plano</h1>
            <Link href="/dashboard">
                <Button variant="outline">Voltar</Button>
            </Link>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Plano Atual</CardTitle>
            </CardHeader>

            <CardContent class="space-y-4">
                <div><strong>Plano:</strong> {{ tenant.plan }}</div>

                <!-- UX: banner do downgrade agendado -->
                <div
                    v-if="tenant.scheduled_plan"
                    class="rounded bg-yellow-50 p-3 text-sm text-yellow-800"
                >
                    Downgrade agendado para
                    <strong>{{ tenant.scheduled_plan }}</strong>
                    em {{ tenant.scheduled_plan_at }}
                </div>

                <div class="space-y-2">
                    <select v-model="selectedPlan" class="rounded border p-2">
                        <option value="free">Free</option>
                        <option value="pro">Pro</option>
                        <option value="enterprise">Enterprise</option>
                    </select>

                    <div class="flex gap-2">
                        <Button @click="updatePlan">Atualizar Plano</Button>

                        <Button
                            variant="destructive"
                            @click="cancelSubscription"
                        >
                            Cancelar subscrição
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
