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
        Schema::create('unit_payment_logs', function (Blueprint $table) {
            $table->id();
            // Who did the change
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Polymorphic log target (installment, payment, invoice)
            $table->string('model_type'); // ex: App\Models\UnitPaymentInstallment
            $table->unsignedBigInteger('model_id');

            // Action details
            $table->enum('action', ['created','updated','deleted','status_changed']);
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_payment_logs');
    }
};
