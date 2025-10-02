<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `unit_payment_installments`
            MODIFY `status` ENUM('pending','partial','paid','unpaid','overdue') NOT NULL DEFAULT 'unpaid'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `unit_payment_installments`
            MODIFY `status` ENUM('pending','paid','unpaid','overdue') NOT NULL DEFAULT 'unpaid'");
    }
};
