<script setup lang="ts">
import { router, Link } from '@inertiajs/vue3'; // ADD Link
import { ref } from 'vue';

const props = defineProps({
    type: String,
    filters: Object,
    entities: Object,
    countries: Array,
});

const search = ref(props.filters?.search ?? '');

function submitSearch() {
    router.get(
        '/entities',
        { type: props.type, search: search.value },
        { preserveState: true },
    );
}

function goType(t) {
    router.get(
        '/entities',
        { type: t, search: search.value },
        { preserveState: true },
    );
}
</script>

<template>
    <div class="space-y-4 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Entidades</h1>

            <div class="space-x-2">
                <!--  4.1 Botão Nova Entidade -->
                <Link href="/entities/create" class="rounded border px-3 py-2">
                    Nova Entidade
                </Link>

                <button
                    class="rounded border px-3 py-2"
                    @click="goType('client')"
                >
                    Clientes
                </button>
                <button
                    class="rounded border px-3 py-2"
                    @click="goType('supplier')"
                >
                    Fornecedores
                </button>
                <button class="rounded border px-3 py-2" @click="goType(null)">
                    Todas
                </button>
            </div>
        </div>

        <div class="flex gap-2">
            <input
                v-model="search"
                class="w-full rounded border px-3 py-2"
                placeholder="Pesquisar por nome, NIF ou email"
            />
            <button class="rounded border px-4 py-2" @click="submitSearch">
                Pesquisar
            </button>
        </div>

        <div class="overflow-hidden rounded border">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">Nº</th>
                        <th class="p-3 text-left">Nome</th>
                        <th class="p-3 text-left">NIF</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Tipo</th>
                        <th class="p-3 text-left">Ativo</th>
                        <th class="p-3 text-left">Ações</th>
                        <!-- ADD coluna -->
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="e in entities.data" :key="e.id" class="border-t">
                        <td class="p-3">{{ e.number }}</td>
                        <td class="p-3">{{ e.name }}</td>
                        <td class="p-3">{{ e.nif }}</td>
                        <td class="p-3">{{ e.email }}</td>
                        <td class="p-3">
                            <span v-if="e.is_client">Cliente</span>
                            <span v-if="e.is_client && e.is_supplier"> / </span>
                            <span v-if="e.is_supplier">Fornecedor</span>
                        </td>
                        <td class="p-3">{{ e.active ? 'Sim' : 'Não' }}</td>

                        <!--  4.2 Link Editar -->
                        <td class="p-3">
                            <Link
                                :href="`/entities/${e.id}/edit`"
                                class="underline"
                            >
                                Editar
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex gap-2">
            <button
                v-for="link in entities.links"
                :key="link.label"
                class="rounded border px-3 py-2"
                :disabled="!link.url"
                v-html="link.label"
                @click="
                    link.url &&
                    router.visit(
                        new URL(link.url).pathname + new URL(link.url).search,
                    )
                "
            />
        </div>
    </div>
</template>
