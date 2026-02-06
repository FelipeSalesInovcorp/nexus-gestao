<script setup lang="ts">
import { router, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table'

type Role = { id: number; name: string }

type ContactRow = {
    id: number
    name: string
    email?: string | null
    phone?: string | null
    is_primary: boolean
    role_name?: string | null
    entity?: { id: number; number?: string | number | null; name: string } | null
}

type PaginationLink = { url: string | null; label: string; active: boolean }

const props = defineProps<{
    filters: { search?: string | null; role_id?: string | number | null; primary?: boolean }
    contacts: { data: ContactRow[]; links: PaginationLink[] }
    roles: Role[]
}>()

const search = ref(props.filters?.search ?? '')
const roleId = ref(props.filters?.role_id ?? '')
const primary = ref(!!props.filters?.primary)

function applyFilters() {
    router.get(
        '/contacts',
        {
            search: search.value || null,
            role_id: roleId.value || null,
            primary: primary.value ? 1 : null,
        },
        { preserveState: true, preserveScroll: true },
    )
}
</script>

<template>
    <div class="space-y-4 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold tracking-tight">Contactos</h1>
            <Link href="/entities" class="text-sm underline">Ver Entidades</Link>
        </div>

        <Card>
            <CardHeader class="pb-2">
                <CardTitle class="text-base">Filtros</CardTitle>
            </CardHeader>

            <CardContent class="space-y-4">
                <div class="grid gap-3 md:grid-cols-3">
                    <div class="space-y-2">
                        <div class="text-sm font-medium">Pesquisa</div>
                        <Input v-model="search" placeholder="Nome, email ou telefone" @keyup.enter="applyFilters" />
                    </div>

                    <div class="space-y-2">
                        <div class="text-sm font-medium">Cargo</div>
                        <select v-model="roleId" class="w-full rounded-md border px-3 py-2">
                            <option value="">—</option>
                            <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.name }}</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-3">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" v-model="primary" />
                            Só principais
                        </label>

                        <Button variant="outline" @click="applyFilters">Aplicar</Button>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border shadow-sm">
                    <Table>
                        <TableHeader class="bg-gray-100 dark:bg-gray-800">
                            <TableRow>
                                <TableHead>Nome</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Telefone</TableHead>
                                <TableHead>Cargo</TableHead>
                                <TableHead>Principal</TableHead>
                                <TableHead>Entidade</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <TableRow v-for="c in props.contacts.data" :key="c.id">
                                <TableCell class="font-medium">{{ c.name }}</TableCell>
                                <TableCell>{{ c.email ?? '—' }}</TableCell>
                                <TableCell>{{ c.phone ?? '—' }}</TableCell>
                                <TableCell>{{ c.role_name ?? '—' }}</TableCell>

                                <TableCell>
                                    <Badge :variant="c.is_primary ? 'default' : 'secondary'">
                                        {{ c.is_primary ? 'Sim' : 'Não' }}
                                    </Badge>
                                </TableCell>

                                <TableCell>
                                    <div v-if="c.entity">
                                        <div class="text-sm font-medium">{{ c.entity.name }}</div>
                                        <div class="text-xs text-muted-foreground">Nº {{ c.entity.number }}</div>
                                    </div>
                                    <span v-else>—</span>
                                </TableCell>

                                <TableCell class="text-right">
                                    <Link v-if="c.entity" :href="`/entities/${c.entity.id}/edit`" class="underline">
                                        Editar Entidade
                                    </Link>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="props.contacts.data.length === 0">
                                <TableCell colspan="7" class="py-8 text-center text-muted-foreground">
                                    Nenhum contacto encontrado.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Button v-for="link in props.contacts.links" :key="link.label" variant="outline"
                        :disabled="!link.url" @click="link.url && router.visit(link.url)">
                        {{ link.label.replace(/&laquo;|&raquo;|&lsaquo;|&rsaquo;/g, '') }}
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
