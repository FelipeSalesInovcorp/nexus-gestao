<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type TaxRateRow = { id: number; name: string; rate: number };

const props = defineProps<{
    taxRates: TaxRateRow[];
}>();

const form = useForm({
    sku: '',
    name: '',
    description: '',
    price: 0,
    tax_rate_id: props.taxRates?.[0]?.id ?? null,
    active: true,
});

function submit() {
    form.post('/config/products', {
        onSuccess: () => alert('Artigo criado com sucesso!'),
    });
}
</script>

<template>
    <div class="max-w-3xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Novo Artigo</h1>

            <Link href="/config/products">
                <Button variant="outline">Voltar</Button>
            </Link>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Dados do artigo</CardTitle>
            </CardHeader>

            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>SKU</Label>
                            <Input
                                v-model="form.sku"
                                placeholder="Ex: ART-001"
                            />
                            <p
                                v-if="form.errors.sku"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.sku }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Preço *</Label>
                            <Input
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                            <p
                                v-if="form.errors.price"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.price }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label>Nome *</Label>
                        <Input v-model="form.name" />
                        <p v-if="form.errors.name" class="text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label>Descrição</Label>
                        <textarea
                            rows="3"
                            v-model="form.description"
                            class="w-full rounded-md border px-3 py-2"
                        />
                        <p
                            v-if="form.errors.description"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label>IVA *</Label>
                        <select
                            v-model="form.tax_rate_id"
                            class="w-full rounded-md border px-3 py-2"
                        >
                            <option
                                v-for="t in props.taxRates"
                                :key="t.id"
                                :value="t.id"
                            >
                                {{ t.name }} ({{ t.rate }}%)
                            </option>
                        </select>
                        <p
                            v-if="form.errors.tax_rate_id"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.tax_rate_id }}
                        </p>
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" v-model="form.active" />
                        Ativo
                    </label>

                    <Button type="submit" :disabled="form.processing"
                        >Guardar</Button
                    >
                </form>
            </CardContent>
        </Card>
    </div>
</template>
