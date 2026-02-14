<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('calendar_event_actions', function (Blueprint $table) {
             // Add the deleted_at column if it doesn't exist
            if (!Schema::hasColumn('calendar_event_actions', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_event_actions', function (Blueprint $table) {
            // Drop the deleted_at column if it exists
            if (Schema::hasColumn('calendar_event_actions', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
