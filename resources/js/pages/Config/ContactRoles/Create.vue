<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

const form = useForm({
    name: '',
});

function submit() {
    form.post('/config/contact-roles');
}
</script>

<template>
    <div class="max-w-xl space-y-6 p-6">
        <div class="flex justify-between">
            <h1 class="text-2xl font-semibold">Nova Função</h1>

            <Link href="/config/contact-roles">
                <Button variant="outline">Voltar</Button>
            </Link>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Dados</CardTitle>
            </CardHeader>

            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <Input placeholder="Ex: Financeiro" v-model="form.name" />

                    <p v-if="form.errors.name" class="text-sm text-red-600">
                        {{ form.errors.name }}
                    </p>

                    <Button :disabled="form.processing"> Criar </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
