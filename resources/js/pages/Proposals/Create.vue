<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
    clients: Array<{ id: number; name: string }>;
}>();

const today = new Date().toISOString().slice(0, 10);
const validDefault = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000)
    .toISOString()
    .slice(0, 10);

const form = useForm({
    number: '',
    proposal_date: today,
    valid_until: validDefault,
    entity_id: '',
    status: 'draft',
    notes: '',
});

function submit() {
    form.post('/proposals');
}
</script>

<template>
    <div class="max-w-3xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Nova proposta</h1>

            <Link href="/proposals">
                <Button variant="outline">Voltar</Button>
            </Link>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Dados base</CardTitle>
            </CardHeader>

            <CardContent>
                <form class="space-y-4" @submit.prevent="submit">
                    <div class="space-y-2">
                        <Label>Cliente *</Label>
                        <select
                            v-model="form.entity_id"
                            class="w-full rounded-md border px-3 py-2"
                        >
                            <option :value="''">—</option>
                            <option
                                v-for="c in props.clients"
                                :key="c.id"
                                :value="c.id"
                            >
                                {{ c.name }}
                            </option>
                        </select>
                        <p
                            v-if="form.errors.entity_id"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.entity_id }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>Número</Label>
                            <Input
                                v-model="form.number"
                                placeholder="PROP-0001 (opcional)"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label>Data da Proposta</Label>
                            <Input v-model="form.proposal_date" type="date" />
                            <p
                                v-if="form.errors.proposal_date"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.proposal_date }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>Validade</Label>
                            <Input v-model="form.valid_until" type="date" />
                            <p
                                v-if="form.errors.valid_until"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.valid_until }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Estado</Label>
                            <select
                                v-model="form.status"
                                class="w-full rounded-md border px-3 py-2"
                            >
                                <option value="draft">Rascunho</option>
                                <option value="closed">Fechado</option>
                            </select>
                            <p
                                v-if="form.errors.status"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label>Observações</Label>
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            class="w-full rounded-md border px-3 py-2"
                        />
                        <p
                            v-if="form.errors.notes"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.notes }}
                        </p>
                    </div>

                    <Button type="submit" :disabled="form.processing"
                        >Criar</Button
                    >
                </form>
            </CardContent>
        </Card>
    </div>
</template>
