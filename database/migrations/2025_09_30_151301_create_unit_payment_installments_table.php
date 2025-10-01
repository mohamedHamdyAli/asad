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
        Schema::create('unit_payment_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_payment_id')->constrained('unit_payments')->onDelete('cascade');
            $table->string('title'); // installment name (ex: first installment)
            $table->text('description')->nullable(); // installment details
            $table->decimal('percentage', 5, 2)->nullable(); // percentage of the total price
            $table->decimal('amount', 15, 2); // installment amount
            $table->date('milestone_date')->nullable(); // milestone completion date
            $table->date('submission_date')->nullable(); // submission date
            $table->date('consultant_approval_date')->nullable(); // consultant approval date
            $table->date('payment_date')->nullable(); // actual payment date
            $table->date('due_date')->nullable(); // last due date
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending'); // installment status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_payment_installments');
    }
};
