<script setup>
import { router, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  type: String,
  filters: Object,
  entities: Object,
  countries: Array,
})

const search = ref(props.filters?.search ?? '')

function submitSearch() {
  router.get('/entities', { type: props.type, search: search.value }, { preserveState: true })
}

function goType(t) {
  router.get('/entities', { type: t, search: search.value }, { preserveState: true })
}
</script>

<template>
  <div class="p-6 space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Entidades</h1>

      <div class="space-x-2">
        <button class="px-3 py-2 border rounded" @click="goType('client')">Clientes</button>
        <button class="px-3 py-2 border rounded" @click="goType('supplier')">Fornecedores</button>
        <button class="px-3 py-2 border rounded" @click="goType(null)">Todas</button>
      </div>
    </div>

    <div class="flex gap-2">
      <input v-model="search" class="border rounded px-3 py-2 w-full" placeholder="Pesquisar por nome, NIF ou email" />
      <button class="px-4 py-2 border rounded" @click="submitSearch">Pesquisar</button>
    </div>

    <div class="border rounded overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left p-3">Nº</th>
            <th class="text-left p-3">Nome</th>
            <th class="text-left p-3">NIF</th>
            <th class="text-left p-3">Email</th>
            <th class="text-left p-3">Tipo</th>
            <th class="text-left p-3">Ativo</th>
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
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex gap-2">
      <button
        v-for="link in entities.links"
        :key="link.label"
        class="px-3 py-2 border rounded"
        :disabled="!link.url"
        v-html="link.label"
        @click="link.url && router.visit(link.url)"
      />
    </div>
  </div>
</template>
