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
        Schema::create('proposal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposals')->cascadeOnDelete();

            $table->foreignId('product_id')->nullable()->constrained('products');
            $table->string('description');

            $table->decimal('qty', 12, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);

            $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates');
            $table->decimal('tax_rate', 6, 2)->default(0);       // snapshot %

            $table->foreignId('supplier_id')->nullable()->constrained('entities'); // fornecedor por linha
            $table->decimal('cost_price', 12, 2)->nullable();    // custo por linha

            $table->decimal('line_subtotal', 12, 2)->default(0);
            $table->decimal('line_tax', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_items');
    }
};
