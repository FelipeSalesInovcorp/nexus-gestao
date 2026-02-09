<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import { type BreadcrumbItem } from '@/types'


const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
]
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
                <p class="text-muted-foreground">Visão geral do sistema e atalhos rápidos.</p>
            </div>

            <!-- Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card class="bg-slate-900/40 border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle class="text-base">Propostas</CardTitle>
                        <Badge variant="secondary">agora</Badge>
                    </CardHeader>
                    <CardContent class="space-y-1">
                        <div class="text-3xl font-semibold">—</div>
                        <div class="text-sm text-muted-foreground">Criar propostas, linhas e PDF</div>
                    </CardContent>
                </Card>

                <Card class="bg-slate-900/40 border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle class="text-base">Encomendas</CardTitle>
                        <Badge variant="secondary">MVP</Badge>
                    </CardHeader>
                    <CardContent class="space-y-1">
                        <div class="text-3xl font-semibold">—</div>
                        <div class="text-sm text-muted-foreground">Criar/editar + PDF + conversão</div>
                    </CardContent>
                </Card>

                <Card class="bg-slate-900/40 border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle class="text-base">Artigos</CardTitle>
                        <Badge variant="secondary">catálogo</Badge>
                    </CardHeader>
                    <CardContent class="space-y-1">
                        <div class="text-3xl font-semibold">—</div>
                        <div class="text-sm text-muted-foreground">Referência, preço e taxa de IVA</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Grid principal -->
            <div class="grid gap-4 lg:grid-cols-3">
                <!-- Atalhos -->
                <Card class="lg:col-span-2 bg-slate-900/40 border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle>Atalhos rápidos</CardTitle>
                        <span class="text-sm text-muted-foreground">ações</span>
                    </CardHeader>

                    <CardContent class="space-y-3">
                        <div class="flex flex-wrap gap-2">
                            <Link href="/proposals/create">
                                <Button>Nova proposta</Button>
                            </Link>

                            <Link href="/orders/create">
                                <Button variant="outline">Nova encomenda</Button>
                            </Link>

                            <Link href="/config/products/create">
                                <Button variant="outline">Novo artigo</Button>
                            </Link>

                            <Link href="/entities/create">
                                <Button variant="outline">Nova entidade</Button>
                            </Link>
                        </div>

                        <div class="text-sm text-muted-foreground">
                            Fluxo recomendado para demonstração: <b>Proposta → PDF → Converter → Encomenda → PDF</b>.
                        </div>
                    </CardContent>
                </Card>

                <!-- Módulos -->
                <Card class="bg-slate-900/40 border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader>
                        <CardTitle>Módulos</CardTitle>
                    </CardHeader>

                    <CardContent class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span>Entidades</span>
                            <Link href="/entities" class="underline">Abrir</Link>
                        </div>

                        <div class="flex items-center justify-between">
                            <span>Artigos</span>
                            <Link href="/config/products" class="underline">Abrir</Link>
                        </div>

                        <div class="flex items-center justify-between">
                            <span>Propostas</span>
                            <Link href="/proposals" class="underline">Abrir</Link>
                        </div>

                        <div class="flex items-center justify-between">
                            <span>Encomendas</span>
                            <Link href="/orders" class="underline">Abrir</Link>
                        </div>

                        <div class="flex items-center justify-between">
                            <span>Contactos</span>
                            <Link href="/contacts" class="underline">Abrir</Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
