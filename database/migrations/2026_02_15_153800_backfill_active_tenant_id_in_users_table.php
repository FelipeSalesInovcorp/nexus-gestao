<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $tenantId = DB::table('tenants')->where('slug','default')->value('id');
        if (!$tenantId) return;

        DB::table('users')->whereNull('active_tenant_id')->update([
            'active_tenant_id' => $tenantId
        ]);
    }

    public function down(): void
    {
        // não precisa desfazer
    }
};
