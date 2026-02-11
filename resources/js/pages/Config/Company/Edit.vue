<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const page = usePage();

const props = defineProps({
    company: { type: Object, default: null },
});

const currentLogo = computed(() => page.props.company?.logo_url ?? null);

const form = useForm({
    name: props.company?.name ?? '',
    tax_number: props.company?.tax_number ?? '',
    address: props.company?.address ?? '',
    postal_code: props.company?.postal_code ?? '',
    locality: props.company?.locality ?? '',
    logo: null,
    remove_logo: false,
});

function onLogoChange(e) {
    form.logo = e.target.files?.[0] ?? null;
}

function submit() {
    console.log('submit called');

    form.put('/config/company', {
        forceFormData: true,
        preserveScroll: true,
        onError: (errors) => console.log('errors', errors),
        onSuccess: () => console.log('saved'),
    });
}
</script>

<template>
    <div class="max-w-3xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Empresa</h1>
            <Link href="/dashboard">
                <Button variant="outline">Voltar</Button>
            </Link>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Dados da empresa</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <Label>Nome</Label>
                            <Input
                                v-model="form.name"
                                placeholder="Nome da empresa"
                            />
                            <p
                                v-if="form.errors.name"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Número Contribuinte</Label>
                            <Input
                                v-model="form.tax_number"
                                placeholder="NIF"
                            />
                            <p
                                v-if="form.errors.tax_number"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.tax_number }}
                            </p>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <Label>Morada</Label>
                            <Input
                                v-model="form.address"
                                placeholder="Morada"
                            />
                            <p
                                v-if="form.errors.address"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.address }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Código Postal</Label>
                            <Input
                                v-model="form.postal_code"
                                placeholder="XXXX-XXX"
                            />
                            <p
                                v-if="form.errors.postal_code"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.postal_code }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label>Localidade</Label>
                            <Input
                                v-model="form.locality"
                                placeholder="Cidade"
                            />
                            <p
                                v-if="form.errors.locality"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.locality }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <Label>Logotipo</Label>

                        <div v-if="currentLogo" class="flex items-center gap-4">
                            <img
                                :src="currentLogo"
                                alt="Logo"
                                class="h-14 w-14 rounded-md border object-contain"
                            />
                            <div class="flex items-center gap-2">
                                <Checkbox
                                    id="remove_logo"
                                    v-model:checked="form.remove_logo"
                                />
                                <Label for="remove_logo"
                                    >Remover logotipo</Label
                                >
                            </div>
                        </div>

                        <input
                            type="file"
                            accept="image/png,image/jpeg,image/jpg,image/webp"
                            @change="onLogoChange"
                        />
                        <p v-if="form.errors.logo" class="text-sm text-red-600">
                            {{ form.errors.logo }}
                        </p>
                    </div>

                    <Button type="submit" :disabled="form.processing"
                        >Guardar</Button
                    >
                </form>
            </CardContent>
        </Card>
    </div>
</template>
