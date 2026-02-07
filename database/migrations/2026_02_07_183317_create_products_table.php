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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('sku')->nullable()->index();     // código interno
            $table->string('name');                         // nome do artigo
            $table->text('description')->nullable();

            $table->decimal('price', 10, 2)->default(0);    // preço base
            $table->foreignId('tax_rate_id')->constrained('tax_rates');

            $table->boolean('active')->default(true)->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
