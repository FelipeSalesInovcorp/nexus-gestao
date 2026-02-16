<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'plan')) {
                $table->string('plan', 32)->default('trial')->after('slug');
            }
            if (!Schema::hasColumn('tenants', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable()->after('plan');
            }
            if (!Schema::hasColumn('tenants', 'plan_changed_at')) {
                $table->timestamp('plan_changed_at')->nullable()->after('trial_ends_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'plan_changed_at')) $table->dropColumn('plan_changed_at');
            if (Schema::hasColumn('tenants', 'trial_ends_at')) $table->dropColumn('trial_ends_at');
            if (Schema::hasColumn('tenants', 'plan')) $table->dropColumn('plan');
        });
    }
};
