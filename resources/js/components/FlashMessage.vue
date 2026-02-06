<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type Flash = {
    success?: string;
    error?: string;
    message?: string;
};

const page = usePage();

const flash = computed<Flash>(() => {
    const props: any = page.props ?? {};
    return (props.flash ?? {}) as Flash;
});

const text = computed(
    () => flash.value.success || flash.value.error || flash.value.message || '',
);
</script>

<template>
    <div v-if="text" class="mb-4 rounded border p-3 text-sm">
        {{ text }}
    </div>
</template>
