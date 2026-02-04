<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type Flash = {
    success?: string;
    error?: string;
};

const page = usePage();

const flash = computed(() => (page.props.value.flash ?? {}) as Flash);

const success = computed(() => flash.value.success);
const error = computed(() => flash.value.error);
</script>

<template>
    <div class="space-y-2">
        <div
            v-if="success"
            class="rounded border bg-green-50 px-4 py-3 text-green-800"
        >
            {{ success }}
        </div>

        <div
            v-if="error"
            class="rounded border bg-red-50 px-4 py-3 text-red-800"
        >
            {{ error }}
        </div>
    </div>
</template>
