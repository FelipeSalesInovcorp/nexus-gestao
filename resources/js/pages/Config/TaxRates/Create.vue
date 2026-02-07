<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const form = useForm({
    name: '',
    rate: '',
    active: true,
});

function submit() {
    form.post('/config/tax-rates');
}
</script>

<template>
    <div class="max-w-2xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Nova taxa de IVA</h1>
            <Link href="/config/tax-rates">
                <Button variant="outline">Voltar</Button>
            </Link>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Dados</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="space-y-4" @submit.prevent="submit">
                    <div class="space-y-2">
                        <Label>Nome *</Label>
                        <Input v-model="form.name" placeholder="Ex: Normal" />
                        <p v-if="form.errors.name" class="text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label>Taxa (%) *</Label>
                        <Input v-model="form.rate" placeholder="Ex: 23" />
                        <p v-if="form.errors.rate" class="text-sm text-red-600">
                            {{ form.errors.rate }}
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
