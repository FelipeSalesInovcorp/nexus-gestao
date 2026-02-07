<script setup lang="ts">
import { router, useForm, Link } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps({
    entity: Object,
    countries: Array,
    contactRoles: Array,
});

const form = useForm({
    is_client: props.entity.is_client,
    is_supplier: props.entity.is_supplier,
    name: props.entity.name ?? '',
    nif: props.entity.nif ?? '',
    address: props.entity.address ?? '',
    postal_code: props.entity.postal_code ?? '',
    city: props.entity.city ?? '',
    country_id: props.entity.country_id ?? null,
    email: props.entity.email ?? '',
    phone: props.entity.phone ?? '',
    mobile: props.entity.mobile ?? '',
    website: props.entity.website ?? '',
    notes: props.entity.notes ?? '',
    rgpd_consent: !!props.entity.rgpd_consent,
    active: !!props.entity.active,
});

function submit() {
    form.put(`/entities/${props.entity.id}`);
}

/**
 * Contactos
 */
const contactForm = useForm({
    name: '',
    email: '',
    phone: '',
    role: '', //  fallback
    contact_role_id: null as number | null,
    is_primary: false,
});

function addContact() {
    contactForm.post(`/entities/${props.entity.id}/contacts`, {
        preserveScroll: true,
        onSuccess: () => {
            contactForm.reset();
            alert('Contacto criado com sucesso!');
        },
    });
}

function removeContact(contactId: number) {
    if (!confirm('Remover este contacto?')) return;

    router.delete(`/entities/${props.entity.id}/contacts/${contactId}`, {
        preserveScroll: true,
        onSuccess: () => alert('Contacto removido com sucesso!'),
    });
}
</script>

<template>
    <div class="max-w-3xl space-y-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Editar Entidade</h1>

            <Link href="/entities">
                <Button variant="outline">Voltar</Button>
            </Link>
        </div>

        <!-- Card: Dados da entidade -->
        <Card>
            <CardHeader>
                <CardTitle>Dados da entidade</CardTitle>
            </CardHeader>

            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Tipo -->
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="form.is_client" />
                            Cliente
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="form.is_supplier" />
                            Fornecedor
                        </label>
                    </div>

                    <!-- Nome -->
                    <div class="space-y-2">
                        <Label>Nome *</Label>
                        <Input v-model="form.name" />
                        <p v-if="form.errors.name" class="text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- NIF -->
                    <div class="space-y-2">
                        <Label>NIF *</Label>
                        <Input v-model="form.nif" />
                        <p v-if="form.errors.nif" class="text-sm text-red-600">
                            {{ form.errors.nif }}
                        </p>
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <Label>Email</Label>
                        <Input v-model="form.email" />
                        <p
                            v-if="form.errors.email"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Morada -->
                    <div class="space-y-2">
                        <Label>Morada</Label>
                        <Input v-model="form.address" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>Código Postal</Label>
                            <Input
                                v-model="form.postal_code"
                                placeholder="1234-567"
                            />
                            <p
                                v-if="form.errors.postal_code"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.postal_code }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Cidade</Label>
                            <Input v-model="form.city" />
                        </div>
                    </div>

                    <!-- País -->
                    <div class="space-y-2">
                        <Label>País</Label>
                        <select
                            v-model="form.country_id"
                            class="w-full rounded-md border px-3 py-2"
                        >
                            <option :value="null">—</option>
                            <option
                                v-for="c in countries"
                                :key="c.id"
                                :value="c.id"
                            >
                                {{ c.name }}
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>Telefone</Label>
                            <Input v-model="form.phone" />
                        </div>

                        <div class="space-y-2">
                            <Label>Telemóvel</Label>
                            <Input v-model="form.mobile" />
                        </div>
                    </div>

                    <!-- Website -->
                    <div class="space-y-2">
                        <Label>Website</Label>
                        <Input v-model="form.website" />
                        <p
                            v-if="form.errors.website"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.website }}
                        </p>
                    </div>

                    <!-- Notas -->
                    <div class="space-y-2">
                        <Label>Notas</Label>
                        <textarea
                            rows="3"
                            v-model="form.notes"
                            class="w-full rounded-md border px-3 py-2"
                        />
                    </div>

                    <!-- Flags -->
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                v-model="form.rgpd_consent"
                            />
                            Consentimento RGPD
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="form.active" />
                            Ativo
                        </label>
                    </div>

                    <!-- Submit -->
                    <Button type="submit" :disabled="form.processing"
                        >Guardar alterações</Button
                    >
                </form>
            </CardContent>
        </Card>

        <!-- Card: Contactos -->
        <Card>
            <CardHeader>
                <CardTitle>Contactos</CardTitle>
            </CardHeader>

            <CardContent class="space-y-4">
                <!-- Lista -->
                <div v-if="props.entity.contacts?.length" class="space-y-2">
                    <div
                        v-for="c in props.entity.contacts"
                        :key="c.id"
                        class="flex items-center justify-between rounded-md border p-3"
                    >
                        <div>
                            <div class="font-medium">
                                {{ c.name }}
                                <span
                                    v-if="c.is_primary"
                                    class="ml-2 rounded border px-2 py-1 text-xs"
                                >
                                    Principal
                                </span>
                            </div>

                            <!--<div class="text-sm text-muted-foreground">
                                <span v-if="c.role">{{ c.role }} · </span>
                                <span v-if="c.email">{{ c.email }} · </span>
                                <span v-if="c.phone">{{ c.phone }}</span>
                            </div>-->

                            <div class="text-sm text-muted-foreground">
                                <span v-if="c.contact_role?.name"
                                    >{{ c.contact_role.name }} ·
                                </span>
                                <span v-else-if="c.role">{{ c.role }} · </span>
                                <span v-if="c.email">{{ c.email }} · </span>
                                <span v-if="c.phone">{{ c.phone }}</span>
                            </div>
                        </div>

                        <Button variant="outline" @click="removeContact(c.id)"
                            >Remover</Button
                        >
                    </div>
                </div>

                <div v-else class="text-sm text-muted-foreground">
                    Nenhum contacto registado.
                </div>

                <!-- Form adicionar -->
                <div class="space-y-3 rounded-md border p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>Nome *</Label>
                            <Input v-model="contactForm.name" />
                            <p
                                v-if="contactForm.errors.name"
                                class="text-sm text-red-600"
                            >
                                {{ contactForm.errors.name }}
                            </p>
                        </div>

                        <!--<div class="space-y-2">
                            <Label>Cargo</Label>
                                <select v-model="contactForm.contact_role_id" class="w-full rounded-md border px-3 py-2">
                                <option :value="null">— Selecionar cargo —</option>

                                <option v-for="r in props.contactRoles" :key="r.id" :value="r.id">
                                    {{ r.name }}
                                </option>
                            </select>
                        </div>-->

                        <div class="space-y-2">
                            <Label>Cargo</Label>
                            <select
                                v-model="contactForm.contact_role_id"
                                class="w-full rounded-md border px-3 py-2"
                            >
                                <option :value="null">—</option>
                                <option
                                    v-for="r in props.contactRoles"
                                    :key="r.id"
                                    :value="r.id"
                                >
                                    {{ r.name }}
                                </option>
                            </select>
                            <p
                                v-if="contactForm.errors.contact_role_id"
                                class="text-sm text-red-600"
                            >
                                {{ contactForm.errors.contact_role_id }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>Email</Label>
                            <Input v-model="contactForm.email" />
                            <p
                                v-if="contactForm.errors.email"
                                class="text-sm text-red-600"
                            >
                                {{ contactForm.errors.email }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Telefone</Label>
                            <Input v-model="contactForm.phone" />
                        </div>
                    </div>

                    <label class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            v-model="contactForm.is_primary"
                        />
                        Contacto principal
                    </label>

                    <Button
                        type="button"
                        @click="addContact"
                        :disabled="contactForm.processing"
                    >
                        Adicionar contacto
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
