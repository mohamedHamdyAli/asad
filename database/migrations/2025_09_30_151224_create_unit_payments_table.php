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
        Schema::create('unit_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->decimal('total_price', 15, 2); // total unit price
            $table->integer('installments_count'); // total number of installments
            $table->integer('remaining_installments'); // installments not paid yet
            $table->enum('payment_type', ['cash', 'installments'])->default('installments'); // type of payment plan
            $table->enum('overall_status', ['pending', 'in_progress', 'completed', 'overdue'])->default('pending'); // overall status of the plan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_payments');
    }
};
