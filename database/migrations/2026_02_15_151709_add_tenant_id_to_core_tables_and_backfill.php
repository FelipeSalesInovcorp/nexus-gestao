<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    private function addTenantIdColumn(string $table): void
    {
        if (!Schema::hasTable($table)) return;

        Schema::table($table, function (Blueprint $t) use ($table) {
            if (!Schema::hasColumn($table, 'tenant_id')) {
                $t->unsignedBigInteger('tenant_id')->nullable()->index();
            }
        });
    }

    private function addTenantIdFk(string $table): void
    {
        if (!Schema::hasTable($table)) return;
        if (!Schema::hasColumn($table, 'tenant_id')) return;

        // FK name fixo para evitar duplicados
        $fkName = "{$table}_tenant_id_foreign";

        // Se já existir, não cria de novo
        $existing = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = 'tenant_id'
              AND CONSTRAINT_NAME = ?
            LIMIT 1
        ", [$table, $fkName]);

        if (!empty($existing)) return;

        Schema::table($table, function (Blueprint $t) use ($fkName) {
            $t->foreign('tenant_id', $fkName)
                ->references('id')->on('tenants')
                ->nullOnDelete();
        });
    }

    private function backfillTenantId(string $table, int $tenantId): void
    {
        if (!Schema::hasTable($table)) return;
        if (!Schema::hasColumn($table, 'tenant_id')) return;

        DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $tenantId]);
    }

    public function up(): void
    {
        // 1) cria/obtém tenant Default
        $default = DB::table('tenants')->where('slug', 'default')->first();

        if (!$default) {
            $ownerUserId = DB::table('users')->orderBy('id')->value('id');

            $tenantId = DB::table('tenants')->insertGetId([
                'owner_user_id' => $ownerUserId,
                'name' => 'Default',
                'slug' => 'default',
                'settings' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $default = (object) ['id' => $tenantId, 'owner_user_id' => $ownerUserId];
        }

        $tenantId = (int) $default->id;

        // 2) adiciona tenant_id (nullable) nas tabelas core do teu projeto
        $tables = [
            // business
            'entities',
            'contacts',
            'proposals',
            'proposal_items',
            'orders',
            'order_items',
            'supplier_orders',
            'supplier_order_items',
            'supplier_invoices',
            'calendar_events',

            // tenant config
            'company_settings',
            'products',
            'tax_rates',
            'contact_roles',
            'calendar_event_types',
            'calendar_event_actions',

            // logs (recomendado)
            'activity_log',
        ];

        foreach ($tables as $table) {
            $this->addTenantIdColumn($table);
        }

        // 3) backfill em tudo
        foreach ($tables as $table) {
            $this->backfillTenantId($table, $tenantId);
        }

        // 4) membership inicial (owner/admin)
        $ownerUserId = DB::table('users')->orderBy('id')->value('id');
        if ($ownerUserId) {
            $exists = DB::table('tenant_user')
                ->where('tenant_id', $tenantId)
                ->where('user_id', $ownerUserId)
                ->exists();

            if (!$exists) {
                DB::table('tenant_user')->insert([
                    'tenant_id' => $tenantId,
                    'user_id' => $ownerUserId,
                    'role' => 'owner',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // seta tenant ativo para users sem active_tenant_id
            /*DB::table('users')->whereNull('active_tenant_id')->update(['active_tenant_id' => $tenantId]);*/

            if (Schema::hasColumn('users', 'active_tenant_id')) {
                DB::table('users')->whereNull('active_tenant_id')->update(['active_tenant_id' => $tenantId]);
            }

        }

        // 5) FK (opcional mas recomendado) — só depois do backfill
        foreach ($tables as $table) {
            $this->addTenantIdFk($table);
        }
    }

    public function down(): void
    {
        // Remove FKs e colunas (se existirem)
        $tables = [
            'entities','contacts','proposals','proposal_items','orders','order_items',
            'supplier_orders','supplier_order_items','supplier_invoices','calendar_events',
            'company_settings','products','tax_rates','contact_roles',
            'calendar_event_types','calendar_event_actions','activity_log',
        ];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'tenant_id')) continue;

            Schema::table($table, function (Blueprint $t) use ($table) {
                // drop FK se existir
                $fk = "{$table}_tenant_id_foreign";
                try { $t->dropForeign($fk); } catch (\Throwable $e) {}
                try { $t->dropIndex([$table.'_tenant_id_index']); } catch (\Throwable $e) {}
                try { $t->dropColumn('tenant_id'); } catch (\Throwable $e) {}
            });
        }

        // remove coluna users.active_tenant_id (se existir)
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'active_tenant_id')) {
            Schema::table('users', function (Blueprint $t) {
                try { $t->dropConstrainedForeignId('active_tenant_id'); } catch (\Throwable $e) {}
            });
        }

        Schema::dropIfExists('tenant_user');
        Schema::dropIfExists('tenants');
    }
};
