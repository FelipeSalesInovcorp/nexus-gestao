<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantEvent;
use Illuminate\Support\Carbon;

class TenantPlanService
{
    public function startTrial(Tenant $tenant, ?int $userId = null, int $days = 14): void
    {
        $from = $tenant->plan ?? null;

        $tenant->forceFill([
            'plan' => 'trial',
            'trial_ends_at' => now()->addDays($days),
            'plan_changed_at' => now(),
            'scheduled_plan' => null,
            'scheduled_plan_at' => null,
        ])->save();

        $this->logEvent($tenant, $userId, 'trial_started', $from, 'trial', [
            'days' => $days,
            'trial_ends_at' => $tenant->trial_ends_at?->toISOString(),
        ]);
    }

    public function endTrial(Tenant $tenant, ?int $userId = null, string $fallbackPlan = 'free'): void
    {
        $from = $tenant->plan ?? null;

        $tenant->forceFill([
            'plan' => $fallbackPlan,
            'trial_ends_at' => null,
            'plan_changed_at' => now(),
        ])->save();

        $this->logEvent($tenant, $userId, 'trial_ended', $from, $fallbackPlan);
    }

    public function upgradeNow(Tenant $tenant, string $to, ?int $userId = null): void
    {
        $from = (string) ($tenant->plan ?? 'free');
        if ($from === $to) return;

        $tenant->forceFill([
            'plan' => $to,
            'trial_ends_at' => null,
            'plan_changed_at' => now(),
            'scheduled_plan' => null,
            'scheduled_plan_at' => null,
        ])->save();

        $this->logEvent($tenant, $userId, 'upgrade', $from, $to);
        $this->logEvent($tenant, $userId, 'plan_changed', $from, $to);
    }

    public function scheduleDowngrade(Tenant $tenant, string $to, ?int $userId = null, ?Carbon $when = null): void
    {
        $from = (string) ($tenant->plan ?? 'free');
        if ($from === $to) return;

        // simular “próximo ciclo” como 30 dias após a mudança atual (ou a partir de agora)
        $when ??= now()->addDays(30);

        $tenant->forceFill([
            'scheduled_plan' => $to,
            'scheduled_plan_at' => $when,
        ])->save();

        $this->logEvent($tenant, $userId, 'downgrade_scheduled', $from, $to, [
            'scheduled_plan_at' => $when->toISOString(),
        ]);
    }

    public function applyScheduledDowngradeIfDue(Tenant $tenant, ?int $userId = null): void
    {
        if (!$tenant->scheduled_plan || !$tenant->scheduled_plan_at) return;

        if ($tenant->scheduled_plan_at->isFuture()) return;

        $from = (string) ($tenant->plan ?? 'free');
        $to = (string) $tenant->scheduled_plan;

        $tenant->forceFill([
            'plan' => $to,
            'plan_changed_at' => now(),
            'scheduled_plan' => null,
            'scheduled_plan_at' => null,
        ])->save();

        $this->logEvent($tenant, $userId, 'downgrade_applied', $from, $to);
        $this->logEvent($tenant, $userId, 'plan_changed', $from, $to);
    }

    private function logEvent(Tenant $tenant, ?int $userId, string $type, $from, $to, array $meta = []): void
    {
        TenantEvent::create([
            'tenant_id' => $tenant->id,
            'user_id' => $userId,
            'type' => $type,
            'from' => $from,
            'to' => $to,
            'meta' => $meta,
        ]);
    }

    public function trialDaysLeft(Tenant $tenant): ?int
    {
        if (!$tenant->trial_ends_at) return null;

        $ends = Carbon::parse($tenant->trial_ends_at);
        if ($ends->isPast()) return 0;

        return max(0, now()->diffInDays($ends, false) + (now()->diffInHours($ends, false) % 24 > 0 ? 1 : 0));
    }

    public function scheduleCancellation(Tenant $tenant, ?int $userId = null, ?Carbon $when = null): void
    {
        $from = (string) ($tenant->plan ?? 'free');

        // free, não faz sentido cancelar
        if ($from === 'free') return;

        // simular fim do ciclo: 30 dias após a última mudança (ou a partir de agora)
        $base = $tenant->plan_changed_at ? Carbon::parse($tenant->plan_changed_at) : now();
        $when ??= $base->copy()->addDays(30);

        $tenant->forceFill([
            'scheduled_plan' => 'free',
            'scheduled_plan_at' => $when,
        ])->save();

        $this->logEvent($tenant, $userId, 'cancellation_scheduled', $from, 'free', [
            'scheduled_plan_at' => $when->toISOString(),
            'base' => $base->toISOString(),
        ]);
    }

    public function applyCancellationIfDue(Tenant $tenant, ?int $userId = null): void
    {
        // cancelamento é um caso particular de scheduled_plan = free
        if (($tenant->scheduled_plan ?? null) !== 'free') return;
        if (!$tenant->scheduled_plan_at) return;
        if ($tenant->scheduled_plan_at->isFuture()) return;

        $from = (string) ($tenant->plan ?? 'free');
        if ($from === 'free') {
            // já está free, só limpa agendamento
            $tenant->forceFill([
                'scheduled_plan' => null,
                'scheduled_plan_at' => null,
            ])->save();
            return;
        }

        $tenant->forceFill([
            'plan' => 'free',
            'trial_ends_at' => null,
            'plan_changed_at' => now(),
            'scheduled_plan' => null,
            'scheduled_plan_at' => null,
        ])->save();

        $this->logEvent($tenant, $userId, 'cancellation_applied', $from, 'free');
        $this->logEvent($tenant, $userId, 'plan_changed', $from, 'free');
    }
}
