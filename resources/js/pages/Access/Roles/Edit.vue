<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
    role: { id: number; name: string };
    rolePermissions: string[];
    permissionGroups: Record<string, string[]>;
}>();

const form = useForm({
    name: props.role.name,
    permissions: [...(props.rolePermissions ?? [])] as string[],
});

function togglePermission(p: string, checked: boolean) {
    if (checked) {
        if (!form.permissions.includes(p)) form.permissions.push(p);
    } else {
        form.permissions = form.permissions.filter((x) => x !== p);
    }
}

function toggleGroup(groupPerms: string[], checked: boolean) {
    if (checked) {
        for (const p of groupPerms)
            if (!form.permissions.includes(p)) form.permissions.push(p);
    } else {
        form.permissions = form.permissions.filter(
            (p) => !groupPerms.includes(p),
        );
    }
}

function submit() {
    form.put(`/access/roles/${props.role.id}`, { preserveScroll: true });
}

const selectedCount = computed(() => form.permissions.length);
</script>

<template>
    <div class="max-w-4xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Editar role</h1>

            <Link href="/access/roles">
                <Button variant="outline">Voltar</Button>
            </Link>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Dados</CardTitle>
            </CardHeader>

            <CardContent>
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="space-y-2">
                        <Label>Nome</Label>
                        <Input v-model="form.name" />
                        <p v-if="form.errors.name" class="text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label>Permissões ({{ selectedCount }})</Label>

                        <div class="space-y-4">
                            <div
                                v-for="(perms, group) in props.permissionGroups"
                                :key="group"
                                class="rounded-md border p-4"
                            >
                                <div
                                    class="flex items-center justify-between gap-2"
                                >
                                    <div class="font-medium">{{ group }}</div>

                                    <label
                                        class="flex items-center gap-2 text-sm"
                                    >
                                        <input
                                            type="checkbox"
                                            :checked="
                                                perms.every((p) =>
                                                    form.permissions.includes(
                                                        p,
                                                    ),
                                                )
                                            "
                                            @change="
                                                toggleGroup(
                                                    perms,
                                                    (
                                                        $event.target as HTMLInputElement
                                                    ).checked,
                                                )
                                            "
                                        />
                                        <span>Selecionar tudo</span>
                                    </label>
                                </div>

                                <div
                                    class="mt-3 grid grid-cols-1 gap-2 md:grid-cols-2"
                                >
                                    <label
                                        v-for="p in perms"
                                        :key="p"
                                        class="flex items-center gap-2 text-sm"
                                    >
                                        <input
                                            type="checkbox"
                                            :checked="
                                                form.permissions.includes(p)
                                            "
                                            @change="
                                                togglePermission(
                                                    p,
                                                    (
                                                        $event.target as HTMLInputElement
                                                    ).checked,
                                                )
                                            "
                                        />
                                        <span>{{ p }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <p
                            v-if="form.errors.permissions"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.permissions }}
                        </p>
                    </div>

                    <Button type="submit" :disabled="form.processing"
                        >Guardar alterações</Button
                    >
                </form>
            </CardContent>
        </Card>
    </div>
</template>
