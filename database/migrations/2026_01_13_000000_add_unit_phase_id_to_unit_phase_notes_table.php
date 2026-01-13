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
        Schema::table('unit_phase_notes', function (Blueprint $table) {
            $table->foreignId('unit_phase_id')->nullable()->after('unit_id')->constrained('unit_phases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_phase_notes', function (Blueprint $table) {
            $table->dropForeign(['unit_phase_id']);
            $table->dropColumn('unit_phase_id');
        });
    }
};
