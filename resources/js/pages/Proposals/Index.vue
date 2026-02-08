<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3'

import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'

type ProposalRow = {
    id: number
    number: string
    proposal_date: string | null
    valid_until: string | null
    status: 'draft' | 'closed'
    total: number | string | null
    entity?: { id: number; name: string } | null
}

type PaginationLink = { url: string | null; label: string; active: boolean }

const props = defineProps<{
    proposals: { data: ProposalRow[]; links: PaginationLink[] }
}>()

function destroyProposal(id: number) {
    if (!confirm('Apagar esta proposta?')) return
    router.delete(`/proposals/${id}`, { preserveScroll: true })
}
</script>

<template>
    <div class="space-y-4 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold tracking-tight">Propostas</h1>

            <Link href="/proposals/create">
                <Button>Nova Proposta</Button>
            </Link>
        </div>

        <Card>
            <CardHeader class="pb-2">
                <CardTitle class="text-base">Lista</CardTitle>
            </CardHeader>

            <CardContent class="space-y-4">
                <div class="overflow-hidden rounded-xl border shadow-sm">
                    <Table>
                        <TableHeader class="bg-gray-100 dark:bg-gray-800">
                            <TableRow>
                                <TableHead>Data</TableHead>
                                <TableHead>Número</TableHead>
                                <TableHead>Validade</TableHead>
                                <TableHead>Cliente</TableHead>
                                <TableHead class="text-right">Valor Total</TableHead>
                                <TableHead>Estado</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <TableRow v-for="p in props.proposals.data" :key="p.id">
                                <TableCell>{{ p.proposal_date ?? '—' }}</TableCell>
                                <TableCell class="font-medium">{{ p.number }}</TableCell>
                                <TableCell>{{ p.valid_until ?? '—' }}</TableCell>
                                <TableCell>{{ p.entity?.name ?? '—' }}</TableCell>
                                <TableCell class="text-right">{{ p.total ?? '0.00' }}</TableCell>
                                <TableCell>
                                    <Badge :variant="p.status === 'closed' ? 'default' : 'secondary'">
                                        {{ p.status === 'closed' ? 'Fechado' : 'Rascunho' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right space-x-3">
                                    <Link :href="`/proposals/${p.id}/edit`" class="underline">
                                        Editar
                                    </Link>
                                    <button class="underline text-red-600" @click="destroyProposal(p.id)">
                                        Apagar
                                    </button>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="props.proposals.data.length === 0">
                                <TableCell colspan="8" class="py-8 text-center text-muted-foreground">
                                    Nenhuma proposta encontrada.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Button
                        v-for="link in props.proposals.links"
                        :key="link.label"
                        variant="outline"
                        :disabled="!link.url"
                        @click="link.url && router.visit(link.url)"
                    >
                        {{ link.label.replace(/&laquo;|&raquo;|&lsaquo;|&rsaquo;/g, '') }}
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
