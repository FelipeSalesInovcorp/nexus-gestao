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
        Schema::table('calendar_events', function (Blueprint $table) {
            if (!Schema::hasColumn('calendar_events', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            }

            if (!Schema::hasColumn('calendar_events', 'entity_id')) {
                $table->foreignId('entity_id')->nullable()->after('user_id')->constrained('entities')->nullOnDelete();
            }

            if (!Schema::hasColumn('calendar_events', 'type_id')) {
                $table->foreignId('type_id')->nullable()->after('entity_id')->constrained('calendar_event_types')->nullOnDelete();
            }

            if (!Schema::hasColumn('calendar_events', 'action_id')) {
                $table->foreignId('action_id')->nullable()->after('type_id')->constrained('calendar_event_actions')->nullOnDelete();
            }

            if (!Schema::hasColumn('calendar_events', 'start_at')) {
                $table->dateTime('start_at')->after('action_id');
            }

            if (!Schema::hasColumn('calendar_events', 'duration')) {
                $table->unsignedInteger('duration')->nullable()->after('start_at'); // minutos
            }

            if (!Schema::hasColumn('calendar_events', 'shared')) {
                $table->boolean('shared')->default(false)->after('duration'); // Partilha
            }

            if (!Schema::hasColumn('calendar_events', 'acknowledged')) {
                $table->boolean('acknowledged')->default(false)->after('shared'); // Conhecimento
            }

            if (!Schema::hasColumn('calendar_events', 'status')) {
                $table->string('status')->default('scheduled')->after('acknowledged'); // Estado
            }

            if (!Schema::hasColumn('calendar_events', 'description')) {
                $table->text('description')->nullable()->after('status');
            }

            if (!Schema::hasColumn('calendar_events', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Para evitar dor de cabeça com FKs em rollback, normalmente não removemos tudo aqui
        // mas deixo o essencial:
        Schema::table('calendar_events', function (Blueprint $table) {
            if (Schema::hasColumn('calendar_events', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
