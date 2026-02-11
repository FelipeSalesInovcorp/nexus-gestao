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
        Schema::create('supplier_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_id')->constrained('entities');
            $table->foreignId('order_id')->constrained('orders');

            $table->unsignedInteger('number'); // incremental
            $table->date('date')->nullable();

            $table->enum('status', ['draft', 'sent', 'completed', 'cancelled'])->default('draft');

            $table->decimal('total', 12, 2)->default(0);

            $table->timestamps();

            $table->unique(['supplier_id', 'order_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_orders');
    }
};
