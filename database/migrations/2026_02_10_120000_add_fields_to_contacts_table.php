<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // "Número" (incremental) - útil para referência interna
            $table->unsignedBigInteger('number')->nullable()->after('id');

            // Apelido (mantemos 'name' como "Nome" para não quebrar o código existente)
            $table->string('surname')->nullable()->after('name');

            // Telemóvel
            $table->string('mobile')->nullable()->after('phone');

            // RGPD + Observações + Estado
            $table->boolean('rgpd_consent')->default(false)->after('is_primary');
            $table->text('notes')->nullable()->after('rgpd_consent');
            $table->boolean('active')->default(true)->after('notes');

            $table->index('number');
            $table->index('surname');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['number']);
            $table->dropIndex(['surname']);
            $table->dropIndex(['active']);

            $table->dropColumn(['number', 'surname', 'mobile', 'rgpd_consent', 'notes', 'active']);
        });
    }
};
