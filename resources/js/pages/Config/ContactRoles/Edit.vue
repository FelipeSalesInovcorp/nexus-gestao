<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
    role: {
        id: number;
        name: string;
        active: boolean;
    };
}>();

const form = useForm({
    name: props.role.name ?? '',
    active: !!props.role.active,
});

function submit() {
    form.put(`/config/contact-roles/${props.role.id}`);
}
</script>

<template>
    <div class="max-w-xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Editar Função</h1>

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
                    <div class="space-y-2">
                        <Label>Nome *</Label>
                        <Input
                            v-model="form.name"
                            placeholder="Ex: Financeiro"
                        />
                        <p v-if="form.errors.name" class="text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" v-model="form.active" />
                        Ativo
                    </label>

                    <Button type="submit" :disabled="form.processing">
                        Guardar alterações
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
