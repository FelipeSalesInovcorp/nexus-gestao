<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type TypeRow = {
    id: number;
    name: string;
    color?: string | null;
    active: boolean;
    created_at?: string | null;
};

const props = defineProps<{
    search: string;
    types: {
        data: TypeRow[];
        links?: any[];
        meta?: any;
    };
}>();

const q = ref(props.search ?? '');
const isOpen = ref(false);
const editing = ref<TypeRow | null>(null);

const form = ref({
    name: '',
    color: '#3b82f6',
    active: true,
});

function openCreate() {
    editing.value = null;
    form.value = { name: '', color: '#3b82f6', active: true };
    isOpen.value = true;
}

function openEdit(row: TypeRow) {
    editing.value = row;
    form.value = {
        name: row.name ?? '',
        color: row.color ?? '#3b82f6',
        active: !!row.active,
    };
    isOpen.value = true;
}

function submit() {
    if (editing.value) {
        router.put(`/config/calendar/types/${editing.value.id}`, form.value, {
            preserveScroll: true,
        });
    } else {
        router.post(`/config/calendar/types`, form.value, {
            preserveScroll: true,
        });
    }
    isOpen.value = false;
}

function destroy(row: TypeRow) {
    if (!confirm(`Remover o tipo "${row.name}"?`)) return;
    router.delete(`/config/calendar/types/${row.id}`, { preserveScroll: true });
}

function applySearch() {
    router.get(
        '/config/calendar/types',
        { search: q.value || undefined },
        { preserveState: true, replace: true },
    );
}

const rows = computed(() => props.types?.data ?? []);

function cleanLabel(label: string) {
    // Laravel pagination labels vêm como "&laquo; Previous"
    return label
        .replace(/&laquo;/g, '«')
        .replace(/&raquo;/g, '»')
        .replace(/Previous/g, 'Anterior')
        .replace(/Next/g, 'Próximo');
}
</script>

<template>
    <div class="max-w-6xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">
                Configurações · Calendário · Tipos
            </h1>

            <div class="flex gap-2">
                <Button @click="openCreate">Novo tipo</Button>
                <Link href="/dashboard"
                    ><Button variant="outline">Voltar</Button></Link
                >
            </div>
        </div>

        <Card>
            <CardHeader
                class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
            >
                <CardTitle>Lista</CardTitle>

                <div class="flex gap-2">
                    <Input
                        class="w-72"
                        placeholder="Pesquisar (nome...)"
                        v-model="q"
                        @keydown.enter.prevent="applySearch"
                    />
                    <Button variant="outline" @click="applySearch"
                        >Pesquisar</Button
                    >
                </div>
            </CardHeader>

            <CardContent>
                <div class="overflow-hidden rounded-lg border">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/40">
                            <tr>
                                <th class="p-2 text-left">Nome</th>
                                <th class="p-2 text-left">Cor</th>
                                <th class="p-2 text-left">Ativo</th>
                                <th class="p-2 text-right">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="r in rows" :key="r.id" class="border-t">
                                <td class="p-2">{{ r.name }}</td>
                                <td class="p-2">
                                    <span
                                        class="inline-flex items-center gap-2"
                                    >
                                        <span
                                            class="h-4 w-4 rounded-sm border"
                                            :style="{
                                                backgroundColor:
                                                    r.color ?? '#3b82f6',
                                            }"
                                        ></span>
                                        <span class="text-muted-foreground">{{
                                            r.color ?? '—'
                                        }}</span>
                                    </span>
                                </td>
                                <td class="p-2">
                                    {{ r.active ? 'Sim' : 'Não' }}
                                </td>
                                <td class="p-2 text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            @click="openEdit(r)"
                                            >Editar</Button
                                        >
                                        <Button
                                            size="sm"
                                            variant="destructive"
                                            @click="destroy(r)"
                                            >Apagar</Button
                                        >
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="rows.length === 0">
                                <td
                                    colspan="4"
                                    class="p-4 text-center text-muted-foreground"
                                >
                                    Sem registos.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação (sem v-html, para não quebrar lint) -->
                <div
                    v-if="props.types?.links?.length"
                    class="mt-4 flex flex-wrap gap-2"
                >
                    <Link
                        v-for="l in props.types.links"
                        :key="l.url ?? l.label"
                        :href="l.url ?? '#'"
                        preserve-scroll
                        :class="[
                            'rounded-md border px-3 py-1 text-sm',
                            l.active
                                ? 'bg-muted font-medium'
                                : 'hover:bg-muted/50',
                            !l.url ? 'pointer-events-none opacity-50' : '',
                        ]"
                    >
                        {{ cleanLabel(l.label) }}
                    </Link>
                </div>
            </CardContent>
        </Card>

        <Dialog v-model:open="isOpen">
            <DialogContent class="max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{
                        editing ? 'Editar tipo' : 'Novo tipo'
                    }}</DialogTitle>
                </DialogHeader>

                <div class="space-y-4">
                    <div class="space-y-2">
                        <Label>Nome</Label>
                        <Input v-model="form.name" />
                    </div>

                    <div class="space-y-2">
                        <Label>Cor</Label>
                        <Input
                            type="text"
                            v-model="form.color"
                            placeholder="#3b82f6"
                        />
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox
                            :checked="form.active"
                            @update:checked="(v: any) => (form.active = !!v)"
                        />
                        <Label>Ativo</Label>
                    </div>
                </div>

                <DialogFooter class="flex justify-end gap-2">
                    <Button
                        variant="outline"
                        type="button"
                        @click="isOpen = false"
                        >Cancelar</Button
                    >
                    <Button type="button" @click="submit">Guardar</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
