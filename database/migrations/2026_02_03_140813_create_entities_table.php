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
        Schema::create('entities', function (Blueprint $table) {

            $table->id();

            // Número interno 
            $table->unsignedBigInteger('number')->unique();

            // Pode ser cliente e fornecedor
            $table->boolean('is_client')->default(false)->index();
            $table->boolean('is_supplier')->default(false)->index();

            $table->string('name')->index();

            // NÃO CIFRAR (precisa ser unique)
            $table->string('nif', 20)->unique();

            // Cifrar depois via Model
            $table->text('address')->nullable();

            $table->string('postal_code', 10)->nullable()->index();
            $table->string('city')->nullable()->index();

            $table->foreignId('country_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // NÃO cifrados (para pesquisa)
            $table->string('email')->nullable()->index();
            $table->string('phone', 30)->nullable();
            $table->string('mobile', 30)->nullable();

            $table->string('website')->nullable();

            // Cifrar
            $table->text('notes')->nullable();

            $table->boolean('rgpd_consent')->default(false);

            $table->boolean('active')->default(true)->index();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
