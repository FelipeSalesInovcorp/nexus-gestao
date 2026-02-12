<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

import { Separator } from '@/components/ui/separator';

type Invoice = {
    id: number;
    number?: string | null;
    supplier?: { name: string; email?: string | null } | null;
    supplier_id?: number | null;
    status?: string | null;
    total?: number | string | null;
    issue_date?: string | null;
    due_date?: string | null;
    paid_at?: string | null;
    proof_path?: string | null;
    document_path?: string | null;
};

const props = defineProps<{ invoice: Invoice }>();

const open = ref(false);
const proofFile = ref<File | null>(null);
const sendEmail = ref(true);
const localError = ref<string | null>(null);

const canDownloadProof = computed(() => !!props.invoice?.proof_path);

function markPaidWithoutProof() {
    localError.value = null;

    router.post(
        `/finance/supplier-invoices/${props.invoice.id}/mark-paid`,
        {},
        { preserveScroll: true },
    );

    open.value = false;
}

function onProofChange(e: Event) {
    const input = e.target as HTMLInputElement;
    proofFile.value = input.files?.[0] ?? null;
    localError.value = null;
}

function markPaidWithProof() {
    localError.value = null;

    if (!proofFile.value) {
        localError.value =
            'Selecione um ficheiro de comprovativo antes de enviar.';
        return;
    }

    router.post(
        `/finance/supplier-invoices/${props.invoice.id}/mark-paid-with-proof`,
        { proof: proofFile.value, send_email: sendEmail.value },
        {
            forceFormData: true,
            preserveScroll: true,
        },
    );

    open.value = false;
}
</script>

<template>
    <div class="max-w-5xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">
                Fatura fornecedor {{ props.invoice.number ?? props.invoice.id }}
            </h1>

            <div class="flex items-center gap-2">
                <a
                    :href="`/finance/supplier-invoices/${props.invoice.id}/download`"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <Button variant="outline" type="button">Documento</Button>
                </a>

                <Link
                    :href="`/finance/supplier-invoices/${props.invoice.id}/edit`"
                >
                    <Button variant="outline" type="button">Editar</Button>
                </Link>

                <Link href="/finance/supplier-invoices">
                    <Button variant="outline" type="button">Voltar</Button>
                </Link>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Resumo</CardTitle>
            </CardHeader>

            <CardContent class="space-y-2 text-sm">
                <div>
                    <b>Fornecedor:</b> {{ props.invoice.supplier?.name ?? '—' }}
                </div>
                <div><b>Estado:</b> {{ props.invoice.status ?? '—' }}</div>
                <div>
                    <b>Data da fatura:</b> {{ props.invoice.issue_date ?? '—' }}
                </div>
                <div>
                    <b>Vencimento:</b> {{ props.invoice.due_date ?? '—' }}
                </div>
                <div><b>Total:</b> {{ props.invoice.total ?? '—' }}</div>
                <div><b>Pago em:</b> {{ props.invoice.paid_at ?? '—' }}</div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Pagamento</CardTitle>
            </CardHeader>

            <CardContent class="space-y-4">
                <Dialog v-model:open="open">
                    <DialogTrigger as-child>
                        <Button type="button">Marcar como paga</Button>
                    </DialogTrigger>

                    <DialogContent class="sm:max-w-lg">
                        <DialogHeader>
                            <DialogTitle>Marcar fatura como paga</DialogTitle>
                            <DialogDescription>
                                Pretende enviar o comprovativo de pagamento ao
                                fornecedor?
                            </DialogDescription>
                        </DialogHeader>

                        <div class="space-y-4">
                            <div class="space-y-3 rounded-lg border p-4">
                                <div class="font-medium">Opção A</div>
                                <div class="text-sm text-muted-foreground">
                                    Marcar como paga <b>sem</b> comprovativo e
                                    sem envio de email.
                                </div>
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="markPaidWithoutProof"
                                >
                                    Marcar como paga (sem comprovativo)
                                </Button>
                            </div>

                            <Separator />

                            <div class="space-y-3 rounded-lg border p-4">
                                <div class="font-medium">Opção B</div>
                                <div class="text-sm text-muted-foreground">
                                    Anexar comprovativo e (opcionalmente) enviar
                                    por email ao fornecedor.
                                </div>

                                <div class="space-y-2">
                                    <Label>Comprovativo</Label>
                                    <Input
                                        type="file"
                                        @change="onProofChange"
                                    />
                                </div>

                                <div class="flex items-center gap-2">
                                    <Checkbox
                                        :checked="sendEmail"
                                        @update:checked="sendEmail = $event"
                                    />
                                    <span class="text-sm"
                                        >Enviar email ao fornecedor</span
                                    >
                                </div>

                                <p
                                    v-if="localError"
                                    class="text-sm text-red-600"
                                >
                                    {{ localError }}
                                </p>

                                <Button
                                    type="button"
                                    @click="markPaidWithProof"
                                >
                                    Enviar comprovativo e marcar como paga
                                </Button>
                            </div>
                        </div>

                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                @click="open = false"
                                >Cancelar</Button
                            >
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

                <div class="flex items-center gap-2">
                    <a
                        v-if="canDownloadProof"
                        :href="`/finance/supplier-invoices/${props.invoice.id}/download-proof`"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <Button variant="outline" type="button"
                            >Comprovativo</Button
                        >
                    </a>

                    <span v-else class="text-sm text-muted-foreground"
                        >Sem comprovativo.</span
                    >
                </div>
            </CardContent>
        </Card>
    </div>
</template>
