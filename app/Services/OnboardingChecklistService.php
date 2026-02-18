<?php

namespace App\Services;

use App\Support\TenantContext;
use Illuminate\Support\Facades\Schema;

class OnboardingChecklistService
{
    public function build(): array
    {
        $tenant = TenantContext::get();
        abort_unless($tenant, 404);

        [$companyOk, $companyHasLogo] = $this->checkCompany();
        $membersCount = $this->membersCount($tenant);
        $usersOk = $membersCount >= 2;

        $rolesOk = $this->tableExists('roles')
            ? \Spatie\Permission\Models\Role::query()->exists()
            : false;

        $calendarTypesOk = $this->tableExists('calendar_event_types')
            ? $this->calendarTypesExist()
            : false;

        $proposalsOk = $this->tableExists('proposals')
            ? $this->proposalsExist()
            : false;

        $eventsOk = $this->tableExists('tenant_events')
            ? \App\Models\TenantEvent::query()->where('tenant_id', $tenant->id)->exists()
            : false;

        $items = [
            $this->item(
                'company',
                'Configurar empresa',
                $companyHasLogo ? 'Nome e logo configurados.' : 'Definir nome e (opcional) logo.',
                $companyOk,
                '/config/company'
            ),
            $this->item(
                'users',
                'Adicionar utilizadores',
                "Membros no tenant: {$membersCount}.",
                $usersOk,
                '/access/users'
            ),
            $this->item(
                'roles',
                'Definir roles/permissões',
                'Configurar o módulo de acessos (roles e permissões).',
                $rolesOk,
                '/access/roles'
            ),
            $this->item(
                'plan',
                'Validar plano e governação',
                'Testar upgrade/downgrade e ver mudanças agendadas.',
                true,
                '/tenant/plan'
            ),
            $this->item(
                'calendar',
                'Configurar calendário',
                'Criar tipos/ações e validar o módulo.',
                $calendarTypesOk,
                '/config/calendar/types'
            ),
            $this->item(
                'proposals',
                'Criar a primeira proposta',
                'Criar uma proposta e confirmar o fluxo.',
                $proposalsOk,
                '/proposals'
            ),
            $this->item(
                'audit',
                'Ver auditoria do tenant',
                'Confirmar eventos de plano e governação.',
                $eventsOk,
                '/tenant/events'
            ),
        ];

        $doneCount = collect($items)->where('done', true)->count();
        $total = count($items);

        return [
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'plan' => $tenant->plan ?? 'free',
            ],
            'progress' => [
                'done' => $doneCount,
                'total' => $total,
                'percent' => $total > 0 ? (int) round(($doneCount / $total) * 100) : 0,
            ],
            'items' => $items,
        ];
    }

    // --------------------
    // Helpers / Checks
    // --------------------

    private function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function checkCompany(): array
    {
        if (!$this->tableExists('company_settings')) {
            return [false, false];
        }

        $company = \App\Models\CompanySetting::query()->first();
        if (!$company) return [false, false];

        $companyOk = (bool) ($company->name);
        $companyHasLogo = !empty($company->logo_path);

        return [$companyOk, $companyHasLogo];
    }

    private function membersCount($tenant): int
    {
        // assumes relation exists: $tenant->users()
        try {
            return (int) $tenant->users()->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function calendarTypesExist(): bool
    {
        // Ajusta se o teu model/tabela tiver outro nome
        return \App\Models\CalendarEventType::query()->exists();
    }

    private function proposalsExist(): bool
    {
        // Ajusta se o teu model/tabela tiver outro nome
        return \App\Models\Proposal::query()->exists();
    }

    private function item(string $key, string $title, string $description, bool $done, string $href): array
    {
        return compact('key', 'title', 'description', 'done', 'href');
    }
}
