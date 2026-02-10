<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const page = usePage();

const companyName = computed(
    () => (page.props.company as any)?.name ?? page.props.name ?? '',
);
const logoUrl = computed(() => (page.props.company as any)?.logo_url ?? null);
</script>

<template>
    <div class="flex items-center gap-2">
        <div
            class="flex aspect-square size-8 items-center justify-center overflow-hidden rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
        >
            <img
                v-if="logoUrl"
                :src="logoUrl"
                alt="Logo"
                class="h-full w-full bg-white object-contain"
            />
            <AppLogoIcon
                v-else
                class="size-5 fill-current text-white dark:text-black"
            />
        </div>
        <div class="grid flex-1 text-left text-sm">
            <span class="mb-0.5 truncate leading-tight font-semibold">{{
                companyName
            }}</span>
        </div>
    </div>
</template>
