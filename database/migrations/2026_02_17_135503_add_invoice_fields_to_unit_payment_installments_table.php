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
        Schema::table('unit_payment_installments', function (Blueprint $table) {
            $table->string('invoice_file')->nullable()->after('status');
            $table->decimal('paid_amount', 15, 2)->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_payment_installments', function (Blueprint $table) {
            $table->dropColumn(['invoice_file', 'paid_amount']);
        });
    }
};
