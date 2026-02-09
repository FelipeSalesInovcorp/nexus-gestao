<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

defineProps<{
    canRegister?: boolean
}>()
</script>

<template>
    <div class="min-h-screen bg-background">
        <!-- Top bar -->
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
            <div class="text-lg font-semibold">Nexus Gestão</div>

            <div class="flex items-center gap-3">
                <Link href="/login" class="text-sm underline">Entrar</Link>

                <Link v-if="canRegister" href="/register">
                    <Button variant="outline">Criar conta</Button>
                </Link>
            </div>
        </div>

        <!-- Hero -->
        <div class="mx-auto max-w-6xl px-6 pb-14 pt-6">
            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle class="text-2xl">Gestão comercial simples e eficiente</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <p class="text-sm text-muted-foreground">
                            Gestão de clientes, artigos, propostas e encomendas com exportação para PDF e conversão
                            automática.
                        </p>

                        <div class="flex flex-wrap gap-2">
                            <Link href="/login">
                                <Button>Entrar</Button>
                            </Link>

                            <Link v-if="canRegister" href="/register">
                                <Button variant="outline">Criar conta</Button>
                            </Link>
                        </div>

                        <div class="text-xs text-muted-foreground">
                            Stack: Laravel + Inertia + Vue + Tailwind.
                        </div>
                    </CardContent>
                </Card>

                <div class="grid gap-4">
                    <Card>
                        <CardHeader>
                            <CardTitle>Propostas</CardTitle>
                        </CardHeader>
                        <CardContent class="text-sm text-muted-foreground">
                            Linhas com auto-preenchimento, cálculos de IVA e PDF.
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Encomendas</CardTitle>
                        </CardHeader>
                        <CardContent class="text-sm text-muted-foreground">
                            Criar encomendas ou converter propostas em 1 clique (ENC-000X).
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Artigos</CardTitle>
                        </CardHeader>
                        <CardContent class="text-sm text-muted-foreground">
                            Produtos com referência, preço e taxa de IVA.
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>

