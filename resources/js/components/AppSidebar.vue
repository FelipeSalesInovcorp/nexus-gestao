<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    LayoutGrid,
    Users,
    UserCog,
    Package,
    Building2,
    FileText,
    Phone,
    ShoppingCart,
    Truck,
    Settings,
    Factory,
    Percent,
} from 'lucide-vue-next';

import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
//import { dashboard } from '@/routes';

import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

/*const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];*/

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Clientes',
        href: '/entities?type=client',
        icon: Users,
    },
    {
        title: 'Fornecedores',
        href: '/entities?type=supplier',
        icon: Factory,
    },
    {
        title: 'Artigos',
        href: '/config/products',
        icon: Package,
    },
    {
        title: 'Entidades',
        href: '/entities',
        icon: Building2,
    },
    {
        title: 'Propostas',
        href: '/proposals',
        icon: FileText,
    },
    {
        title: 'Contactos',
        href: '/contacts',
        icon: Phone,
    },
    {
        title: 'Encomendas',
        href: '/orders',
        icon: ShoppingCart,
    },
    {
        title: 'Encomendas a fornecedores',
        href: '/supplier-orders',
        icon: Truck,
    },
    {
        title: 'Financeiro',
        href: '/finance/supplier-invoices',
        icon: UserCog,
    },

    {
        title: 'Configuração',
        icon: Settings,
        children: [
            {
                title: 'Empresa',
                href: '/config/company',
                icon: Building2,
            },
            {
                title: 'Funções de Contacto',
                href: '/config/contact-roles',
                icon: UserCog,
            },
            {
                title: 'Taxas de IVA',
                href: '/config/tax-rates',
                icon: Percent,
            },
        ],
    },
    {
        title: 'Gestão de Acessos',
        icon: UserCog,
        children: [
            {
                title: 'Utilizadores',
                href: '/access/users',
                icon: Users,
            },
            {
                title: 'Roles',
                href: '/access/roles',
                icon: UserCog,
            },
        ],
    },
];

/*const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];*/
</script>

<template>
    <!--<Sidebar collapsible="icon" variant="inset">-->
    <Sidebar variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <!--<Link :href="dashboard()"-->
                        <Link href="/dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
