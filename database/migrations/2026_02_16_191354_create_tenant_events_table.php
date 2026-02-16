<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenant_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('type'); // trial_started, trial_ended, plan_changed, etc.
            $table->string('from')->nullable(); // ex: trial
            $table->string('to')->nullable();   // ex: pro

            $table->json('meta')->nullable();   // extras (limits, reason, ip, etc.)
            $table->timestamps();

            $table->index(['tenant_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_events');
    }
};
