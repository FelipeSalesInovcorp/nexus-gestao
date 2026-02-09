<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type Role = {
    id: number;
    name: string;
};

type RolesPagination = {
    data: Role[];
};

const { roles } = defineProps<{
    roles: RolesPagination;
}>();

function remove(id: number) {
    if (!confirm('Remover esta função?')) return;

    router.delete(`/config/contact-roles/${id}`);
}
</script>

<template>
    <div class="max-w-5xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Funções de Contacto</h1>

            <div class="flex items-center gap-2">
                <Link href="/config/contact-roles/create">
                    <Button>Nova Função</Button>
                </Link>
                <Link href="/dashboard">
                    <Button variant="outline">Voltar</Button>
                </Link>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Lista</CardTitle>
            </CardHeader>

            <CardContent>
                <div v-if="roles?.data?.length" class="space-y-2">
                    <div
                        v-for="role in roles.data"
                        :key="role.id"
                        class="flex items-center justify-between rounded-md border p-3"
                    >
                        <div class="font-medium">
                            {{ role.name }}
                        </div>

                        <div class="flex gap-2">
                            <Link
                                :href="`/config/contact-roles/${role.id}/edit`"
                            >
                                <Button variant="outline"> Editar </Button>
                            </Link>

                            <Button
                                variant="destructive"
                                @click="remove(role.id)"
                            >
                                Remover
                            </Button>
                        </div>
                    </div>
                </div>

                <div v-else class="text-muted-foreground">
                    Nenhuma função criada.
                </div>
            </CardContent>
        </Card>
    </div>
</template>
