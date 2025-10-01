<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unit_payment_installment_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_payment_installment_id');
            $table->foreign('unit_payment_installment_id', 'upi_invoice_fk')
                ->references('id')
                ->on('unit_payment_installments')
                ->onDelete('cascade');

            $table->decimal('paid_amount', 15, 2); // partial payment amount
            $table->string('invoice_file')->nullable(); // uploaded invoice file
            $table->date('payment_date')->nullable(); // actual payment date
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending'); // invoice status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_payment_installment_invoices');
    }
};
