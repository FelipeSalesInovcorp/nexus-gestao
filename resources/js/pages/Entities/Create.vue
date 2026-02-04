<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';

defineProps({
    countries: Array,
});

const form = useForm({
    is_client: true,
    is_supplier: false,
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
            <Link href="/entities" class="rounded border px-3 py-2"
                >Voltar</Link
            >
        </div>

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

            <div>
                <label class="block text-sm">Nome *</label>
                <input
                    class="w-full rounded border px-3 py-2"
                    v-model="form.name"
                />
                <div v-if="form.errors.name" class="text-sm text-red-600">
                    {{ form.errors.name }}
                </div>
            </div>

            <div>
                <label class="block text-sm">NIF *</label>
                <input
                    class="w-full rounded border px-3 py-2"
                    v-model="form.nif"
                />
                <div v-if="form.errors.nif" class="text-sm text-red-600">
                    {{ form.errors.nif }}
                </div>
            </div>

            <div>
                <label class="block text-sm">Email</label>
                <input
                    class="w-full rounded border px-3 py-2"
                    v-model="form.email"
                />
                <div v-if="form.errors.email" class="text-sm text-red-600">
                    {{ form.errors.email }}
                </div>
            </div>

            <div>
                <label class="block text-sm">Morada</label>
                <input
                    class="w-full rounded border px-3 py-2"
                    v-model="form.address"
                />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm">Código Postal</label>
                    <input
                        class="w-full rounded border px-3 py-2"
                        placeholder="1234-567"
                        v-model="form.postal_code"
                    />
                    <div
                        v-if="form.errors.postal_code"
                        class="text-sm text-red-600"
                    >
                        {{ form.errors.postal_code }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm">Cidade</label>
                    <input
                        class="w-full rounded border px-3 py-2"
                        v-model="form.city"
                    />
                </div>
            </div>

            <div>
                <label class="block text-sm">País</label>
                <select
                    class="w-full rounded border px-3 py-2"
                    v-model="form.country_id"
                >
                    <option :value="null">—</option>
                    <option v-for="c in countries" :key="c.id" :value="c.id">
                        {{ c.name }}
                    </option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm">Telefone</label>
                    <input
                        class="w-full rounded border px-3 py-2"
                        v-model="form.phone"
                    />
                </div>
                <div>
                    <label class="block text-sm">Telemóvel</label>
                    <input
                        class="w-full rounded border px-3 py-2"
                        v-model="form.mobile"
                    />
                </div>
            </div>

            <div>
                <label class="block text-sm">Website</label>
                <input
                    class="w-full rounded border px-3 py-2"
                    v-model="form.website"
                />
                <div v-if="form.errors.website" class="text-sm text-red-600">
                    {{ form.errors.website }}
                </div>
            </div>

            <div>
                <label class="block text-sm">Notas</label>
                <textarea
                    class="w-full rounded border px-3 py-2"
                    rows="3"
                    v-model="form.notes"
                ></textarea>
            </div>

            <div class="flex gap-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" v-model="form.rgpd_consent" />
                    Consentimento RGPD
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" v-model="form.active" />
                    Ativo
                </label>
            </div>

            <button
                class="rounded border px-4 py-2"
                :disabled="form.processing"
            >
                Guardar
            </button>
        </form>
    </div>
</template>
