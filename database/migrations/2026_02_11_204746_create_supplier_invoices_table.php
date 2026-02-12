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
        Schema::create('supplier_invoices', function (Blueprint $table) {

            $table->id();

            $table->foreignId('supplier_id')->constrained('entities')->cascadeOnDelete();
            $table->foreignId('supplier_order_id')->nullable()->constrained('supplier_orders')->nullOnDelete();

            $table->string('number')->nullable(); // nº fatura
            $table->date('issue_date')->nullable();
            $table->date('due_date')->nullable();

            $table->decimal('total', 12, 2)->default(0);

            $table->string('status')->default('pending'); // pending | paid
            $table->timestamp('paid_at')->nullable();

            $table->string('document_path')->nullable();   // ficheiro fatura
            $table->string('proof_path')->nullable();      // comprovativo pagamento

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['supplier_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_invoices');
    }
};
