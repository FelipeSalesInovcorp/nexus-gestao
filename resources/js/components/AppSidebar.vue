<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
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
import { computed } from 'vue';

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

import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

// ✅ Tipo local (não mexe em "@/types")
type NavItemWithPermission = NavItem & {
    permission?: string | string[]; // string = 1 perm; array = any-of
    children?: NavItemWithPermission[];
};

type PageProps = {
    auth?: {
        permissions?: string[];
        roles?: string[];
    };
};

const page = usePage<PageProps>();
const permissions = computed(() => page.props.auth?.permissions ?? []);
//const roles = computed(() => page.props.auth?.roles ?? [])

// Se quiser bypass para admin (opcional), descomente:
//const isAdmin = computed(() => roles.value.includes('admin') || roles.value.includes('Admin'))

function hasPermission(required?: string | string[]) {
    if (!required) return true;
    // if (isAdmin.value) return true

    const reqList = Array.isArray(required) ? required : [required];
    return reqList.some((p) => permissions.value.includes(p));
}

function filterNav(items: NavItemWithPermission[]): NavItemWithPermission[] {
    return (items ?? [])
        .map((item) => {
            // 1) filtra filhos primeiro
            const filteredChildren = item.children
                ? filterNav(item.children)
                : undefined;

            // 2) bloqueia item se não tiver permissão
            if (!hasPermission(item.permission)) return null;

            // 3) se for grupo e ficou sem filhos, remove
            if (
                item.children &&
                (!filteredChildren || filteredChildren.length === 0)
            )
                return null;

            return { ...item, children: filteredChildren };
        })
        .filter(Boolean) as NavItemWithPermission[];
}

const mainNavItems: NavItemWithPermission[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
        permission: 'dashboard.view',
    },
    {
        title: 'Clientes',
        href: '/entities?type=client',
        icon: Users,
        permission: 'entities.view',
    },
    {
        title: 'Fornecedores',
        href: '/entities?type=supplier',
        icon: Factory,
        permission: 'entities.view',
    },
    {
        title: 'Artigos',
        href: '/config/products',
        icon: Package,
        permission: 'config.products.view',
    },
    {
        title: 'Entidades',
        href: '/entities',
        icon: Building2,
        permission: 'entities.view',
    },
    {
        title: 'Propostas',
        href: '/proposals',
        icon: FileText,
        permission: 'proposals.view',
    },
    {
        title: 'Contactos',
        href: '/contacts',
        icon: Phone,
        permission: 'contacts.view',
    },
    {
        title: 'Encomendas',
        href: '/orders',
        icon: ShoppingCart,
        permission: 'orders.view',
    },
    {
        title: 'Encomendas a fornecedores',
        href: '/supplier-orders',
        icon: Truck,
        permission: 'supplier-orders.view',
    },
    {
        title: 'Financeiro',
        href: '/finance/supplier-invoices',
        icon: UserCog,
        permission: 'supplier-invoices.view',
    },

    {
        title: 'Configuração',
        icon: Settings,
        children: [
            {
                title: 'Empresa',
                href: '/config/company',
                icon: Building2,
                permission: 'config.company.update',
            },
            {
                title: 'Funções de Contacto',
                href: '/config/contact-roles',
                icon: UserCog,
                permission: 'config.contact-roles.view',
            },
            {
                title: 'Taxas de IVA',
                href: '/config/tax-rates',
                icon: Percent,
                permission: 'config.tax-rates.view',
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
                permission: 'access.users.view',
            },
            {
                title: 'Roles',
                href: '/access/roles',
                icon: UserCog,
                permission: 'access.roles.view',
            },
            {
                title: 'Logs do Sistema',
                href: '/logs',
                icon: Settings,
                permission: 'logs.view',
            },
        ],
    },
];

// Mantive o teu footerNavItems como está no projeto.
// Se no teu projeto ele estiver noutro ficheiro/const, continua igual.
// Se não existir, ou define aqui, ou remove do template.
</script>

<template>
    <Sidebar variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <!-- ✅ menu já filtrado por permissões -->
            <NavMain :items="filterNav(mainNavItems) as unknown as NavItem[]" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
