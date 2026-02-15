<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const form = useForm({
    name: '',
});

function submit() {
    form.post('/onboarding');
}
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-muted/30 p-6">
        <Card class="w-full max-w-md">
            <CardHeader>
                <CardTitle>Criar sua empresa</CardTitle>
            </CardHeader>

            <CardContent class="space-y-4">
                <div class="space-y-2">
                    <Label>Nome da empresa</Label>
                    <Input v-model="form.name" />
                    <p v-if="form.errors.name" class="text-sm text-destructive">
                        {{ form.errors.name }}
                    </p>
                </div>

                <Button
                    class="w-full"
                    :disabled="form.processing"
                    @click="submit"
                >
                    {{ form.processing ? 'A criar...' : 'Criar e continuar' }}
                </Button>
            </CardContent>
        </Card>
    </div>
</template>
