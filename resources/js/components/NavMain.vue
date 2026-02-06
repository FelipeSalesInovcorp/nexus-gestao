<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { type NavItem } from '@/types';

defineProps<{ items: NavItem[] }>();

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <!--<SidebarGroupLabel>Platform</SidebarGroupLabel>-->
        <SidebarGroupLabel>MENU TESTE</SidebarGroupLabel>

        <SidebarMenuItem>
            <SidebarMenuButton>
                <span style="color: red; font-weight: bold"
                    >MENU TESTE (SE APARECER, ESTÁ OK)</span
                >
            </SidebarMenuButton>
        </SidebarMenuItem>

        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <!-- ✅ ITEM NORMAL -->
                <SidebarMenuItem v-if="!item.children?.length">
                    <SidebarMenuButton
                        as-child
                        :is-active="item.href ? isCurrentUrl(item.href) : false"
                        :tooltip="item.title"
                    >
                        <Link v-if="item.href" :href="item.href">
                            <component v-if="item.icon" :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>

                <!-- ✅ GRUPO (com children) -->
                <SidebarMenuItem v-else>
                    <!-- header do grupo -->
                    <SidebarMenuButton :tooltip="item.title">
                        <component v-if="item.icon" :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </SidebarMenuButton>

                    <!-- submenu simples indentado -->
                    <SidebarMenu class="pl-2">
                        <SidebarMenuItem
                            v-for="child in item.children"
                            :key="child.title"
                        >
                            <SidebarMenuButton
                                as-child
                                :is-active="
                                    child.href
                                        ? isCurrentUrl(child.href)
                                        : false
                                "
                                :tooltip="child.title"
                            >
                                <Link v-if="child.href" :href="child.href">
                                    <span>{{ child.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
