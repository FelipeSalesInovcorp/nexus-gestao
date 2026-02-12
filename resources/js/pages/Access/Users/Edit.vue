<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Role = { id: number; name: string };
type User = { id: number; name: string; email: string };

const props = defineProps<{
    user: User;
    roles: Role[];
    userRoles: string[];
}>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '', // opcional
    roles: [...(props.userRoles ?? [])] as string[],
});

function toggleRole(roleName: string, checked: boolean) {
    if (checked) {
        if (!form.roles.includes(roleName)) form.roles.push(roleName);
    } else {
        form.roles = form.roles.filter((r) => r !== roleName);
    }
}

function submit() {
    form.put(`/access/users/${props.user.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="max-w-3xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Editar utilizador</h1>

            <Link href="/access/users">
                <Button variant="outline">Voltar</Button>
            </Link>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Dados</CardTitle>
            </CardHeader>

            <CardContent>
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label>Nome</Label>
                            <Input v-model="form.name" />
                            <p
                                v-if="form.errors.name"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Email</Label>
                            <Input v-model="form.email" type="email" />
                            <p
                                v-if="form.errors.email"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <Label>Nova password (opcional)</Label>
                            <Input
                                v-model="form.password"
                                type="password"
                                placeholder="Deixe vazio para não alterar"
                            />
                            <p
                                v-if="form.errors.password"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <Label>Roles (grupos)</Label>
                            <div class="space-y-2 rounded-md border p-3">
                                <label
                                    v-for="r in props.roles"
                                    :key="r.id"
                                    class="flex items-center gap-2 text-sm"
                                >
                                    <input
                                        type="checkbox"
                                        :checked="form.roles.includes(r.name)"
                                        @change="
                                            toggleRole(
                                                r.name,
                                                (
                                                    $event.target as HTMLInputElement
                                                ).checked,
                                            )
                                        "
                                    />
                                    <span>{{ r.name }}</span>
                                </label>

                                <p
                                    v-if="props.roles.length === 0"
                                    class="text-sm text-muted-foreground"
                                >
                                    Sem roles criadas.
                                </p>
                            </div>

                            <p
                                v-if="form.errors.roles"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.roles }}
                            </p>
                        </div>
                    </div>

                    <Button type="submit" :disabled="form.processing"
                        >Guardar alterações</Button
                    >
                </form>
            </CardContent>
        </Card>
    </div>
</template>
