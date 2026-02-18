<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
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
    Calendar,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

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

// ---------------- Types ----------------
type TenantOpt = { id: number; name: string; slug?: string | null };

type NavItemWithPermission = NavItem & {
    permission?: string | string[]; // string = 1 perm; array = any-of
    feature?: string;
    children?: NavItemWithPermission[];
};

type PageProps = {
    company?: { name?: string; logo_url?: string | null };
    auth?: {
        user?: { active_tenant_id?: number | null };
        permissions?: string[];
        roles?: string[];
        tenants?: TenantOpt[];
        active_tenant_id?: number | null;
        active_tenant?: { id: number; name: string } | null;
    };
    // Mantém compatibilidade se nalgum sitio vier fora do auth
    tenants?: TenantOpt[];
    activeTenantId?: number | null;
};

const page = usePage<PageProps>();

// ---------------- Company ----------------
/*const companyName = computed(() => page.props.company?.name ?? 'Nexus')
const companyLogoUrl = computed(() => page.props.company?.logo_url ?? null)*/

// ---------------- Auth/Tenant props ----------------
const permissions = computed(() => page.props.auth?.permissions ?? []);
const tenants = computed<TenantOpt[]>(() => {
    return (page.props.auth?.tenants ??
        page.props.tenants ??
        []) as TenantOpt[];
});

const activeTenantId = computed<number | null>(() => {
    const v =
        page.props.auth?.active_tenant_id ??
        page.props.auth?.user?.active_tenant_id ??
        page.props.activeTenantId ??
        null;
    return v ? Number(v) : null;
});

const activeTenantName = computed(() => {
    // se vier o objeto active_tenant do backend, usa-o (mais fiável)
    const direct = page.props.auth?.active_tenant?.name;
    if (direct) return direct;

    const id = activeTenantId.value;
    if (!id) return '';
    return tenants.value.find((t) => Number(t.id) === Number(id))?.name ?? '';
});

// Selector state (sync com prop)
const selectedTenantId = ref<number | ''>('');
const switching = ref(false);
const errorMsg = ref('');

watch(
    () => activeTenantId.value,
    (id) => {
        selectedTenantId.value = id ? Number(id) : '';
    },
    { immediate: true },
);

function switchTenant() {
    errorMsg.value = '';
    const tenantId = selectedTenantId.value;
    if (!tenantId) return;

    switching.value = true;

    router.post(
        '/tenants/switch',
        { tenant_id: Number(tenantId) },
        {
            preserveScroll: true,
            // IMPORTANTE: precisamos recarregar props para refletir o tenant novo
            preserveState: false,
            onFinish: () => {
                switching.value = false;
            },
            onError: (errors) => {
                switching.value = false;
                errorMsg.value =
                    (errors as any)?.tenant_id ||
                    (errors as any)?.message ||
                    'Não foi possível trocar a empresa.';
            },
        },
    );
}

// ---------------- Permissões ----------------
function hasPermission(required?: string | string[]) {
    if (!required) return true;
    const reqList = Array.isArray(required) ? required : [required];
    return reqList.some((p) => permissions.value.includes(p));
}

/*function filterNav(items: NavItemWithPermission[]): NavItemWithPermission[] {
    return (items ?? [])
        .map((item) => {
            const filteredChildren = item.children
                ? filterNav(item.children)
                : undefined;

            if (!hasPermission(item.permission)) return null;

            if (
                item.children &&
                (!filteredChildren || filteredChildren.length === 0)
            ) {
                return null;
            }

            return { ...item, children: filteredChildren };
        })
        .filter(Boolean) as NavItemWithPermission[];
}*/

function filterNav(items: NavItemWithPermission[]): NavItemWithPermission[] {
    return (items ?? [])
        .map((item) => {
            const filteredChildren = item.children
                ? filterNav(item.children)
                : undefined;

            // filtra por permission + feature
            if (!hasPermission(item.permission)) return null;
            if (!hasFeature(item.feature)) return null;

            // se tiver children e nenhum passar, remove o grupo
            if (
                item.children &&
                (!filteredChildren || filteredChildren.length === 0)
            ) {
                return null;
            }

            return { ...item, children: filteredChildren };
        })
        .filter(Boolean) as NavItemWithPermission[];
}

// ---------------- Funcionalidades/Features (ex: logs)
const features = computed<Record<string, boolean>>(() => {
    // se ainda não estiveres a partilhar do backend, fica vazio e não esconde nada
    return ((page.props as any).features ?? {}) as Record<string, boolean>;
});

function hasFeature(required?: string) {
    if (!required) return true;

    const f = features.value ?? {};
    if (f['*'] === true) return true;
    return f[required] === true;
}

// ---------------- Menu ----------------
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
        title: 'Calendário',
        href: '/calendar',
        icon: Calendar,
        permission: 'calendar.view',
    },
    {
        title: 'Tenant',
        icon: Building2,
        children: [
            {
                title: 'Plano do Tenant',
                href: '/tenant/plan',
                icon: Settings,
            },
            {
                title: 'Histórico do Tenant',
                href: '/tenant/events',
                icon: FileText,
                //permission: 'logs.view',
                //feature: 'logs',
            },
            {
                title: 'Onboarding Checklist',
                href: '/onboarding/checklist',
                icon: Settings,
                // permission opcional
            },
        ],
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
            {
                title: 'Calendário - Tipos',
                href: '/config/calendar/types',
                icon: Settings,
                permission: 'calendar.types.view',
            },
            {
                title: 'Calendário - Ações',
                href: '/config/calendar/actions',
                icon: Settings,
                permission: 'calendar.actions.view',
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

//  como no projeto
declare const footerNavItems: any;
</script>

<template>
    <Sidebar variant="inset">
        <SidebarHeader class="px-2">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/dashboard" class="flex items-center gap-3">
                            <!--  AppLogo -->
                            <AppLogo />

                            <!-- nome + tenant ativo (melhora leitura) -->
                            <!--<div class="min-w-0 leading-tight">
                                <div class="truncate text-sm font-semibold">{{ companyName }}</div>
                                <div class="truncate text-xs text-muted-foreground">
                                    {{ activeTenantName ? `Empresa · ${activeTenantName}` : '—' }}
                                </div>
                            </div>-->

                            <div class="flex items-center gap-3">
                                <!--<div
                                    class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-md border bg-background">
                                    <img v-if="companyLogoUrl" :src="companyLogoUrl" alt="logo"
                                        class="h-full w-full object-contain" />
                                    <div v-else class="text-xs font-semibold opacity-70">N</div>
                                </div>-->

                                <div class="min-w-0 flex-1">
                                    <!--<div class="truncate text-sm font-semibold">
                                        {{ companyName }}
                                    </div>-->
                                    <div class="truncate text-xs opacity-70">
                                        {{
                                            activeTenantName
                                                ? `· ${activeTenantName}`
                                                : '—'
                                        }}
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>

            <!-- Seletor de tenant (debaixo do logo) -->
            <div class="mt-3 px-1">
                <div
                    class="mb-1 flex items-center justify-between text-xs text-muted-foreground"
                >
                    <span>Empresa</span>
                    <span v-if="switching" class="text-[11px] opacity-80"
                        >A mudar…</span
                    >
                </div>

                <select
                    v-model="selectedTenantId"
                    class="h-9 w-full rounded-md border bg-background px-2 text-sm"
                    :disabled="switching || tenants.length <= 1"
                    @change="switchTenant"
                >
                    <option v-if="tenants.length === 0" value="" disabled>
                        Sem empresas
                    </option>

                    <option v-for="t in tenants" :key="t.id" :value="t.id">
                        {{ t.name }}
                    </option>
                </select>

                <div v-if="errorMsg" class="mt-1 text-xs text-destructive">
                    {{ errorMsg }}
                </div>
            </div>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="filterNav(mainNavItems) as unknown as NavItem[]" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
