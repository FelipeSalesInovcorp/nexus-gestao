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
        Schema::table('calendar_event_types', function (Blueprint $table) {
            //
            if (!Schema::hasColumn('calendar_event_types', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('calendar_event_types', 'color')) {
                $table->string('color')->nullable()->after('name');
            }
            if (!Schema::hasColumn('calendar_event_types', 'active')) {
                $table->boolean('active')->default(true)->after('color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_event_types', function (Blueprint $table) {
            //
            if (Schema::hasColumn('calendar_event_types', 'active')) $table->dropColumn('active');
            if (Schema::hasColumn('calendar_event_types', 'color')) $table->dropColumn('color');
            if (Schema::hasColumn('calendar_event_types', 'name')) $table->dropColumn('name');
        });
    }
};
