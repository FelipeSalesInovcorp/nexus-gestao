<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'scheduled_plan')) {
                $table->string('scheduled_plan', 32)->nullable()->after('plan_changed_at');
            }
            if (!Schema::hasColumn('tenants', 'scheduled_plan_at')) {
                $table->timestamp('scheduled_plan_at')->nullable()->after('scheduled_plan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'scheduled_plan_at')) $table->dropColumn('scheduled_plan_at');
            if (Schema::hasColumn('tenants', 'scheduled_plan')) $table->dropColumn('scheduled_plan');
        });
    }
};
