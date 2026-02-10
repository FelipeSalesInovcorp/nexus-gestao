<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
    countries: Array<any>;
    type?: 'client' | 'supplier' | null;
}>();

const form = useForm({
    // Pré-seleção conforme menu (Clientes/Fornecedores)
    is_client: props.type === 'supplier' ? false : true,
    is_supplier: props.type === 'supplier' ? true : false,
    name: '',
    nif: '',
    address: '',
    postal_code: '',
    city: '',
    country_id: null,
    email: '',
    phone: '',
    mobile: '',
    website: '',
    notes: '',
    rgpd_consent: false,
    active: true,
});

function submit() {
    form.post('/entities');
}
</script>

<template>
    <div class="max-w-3xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Criar Entidade</h1>

            <Link
                :href="
                    props.type ? `/entities?type=${props.type}` : '/entities'
                "
            >
                <Button variant="outline">Voltar</Button>
            </Link>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Dados da entidade</CardTitle>
            </CardHeader>

            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
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

                    <div class="space-y-2">
                        <Label>Website</Label>
                        <Input v-model="form.website" />
                    </div>

                    <div class="space-y-2">
                        <Label>Notas</Label>
                        <textarea
                            rows="3"
                            v-model="form.notes"
                            class="w-full rounded-md border px-3 py-2"
                        />
                    </div>

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

                    <Button type="submit" :disabled="form.processing">
                        Guardar
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
