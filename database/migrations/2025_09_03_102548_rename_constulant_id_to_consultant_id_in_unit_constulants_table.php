<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_constulants', function (Blueprint $table) {
            $table->dropForeign(['constulant_id']);
            $table->renameColumn('constulant_id', 'consultant_id');
        });

        Schema::table('unit_constulants', function (Blueprint $table) {
            $table->foreign('consultant_id')->references('id')->on('consultants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('unit_constulants', function (Blueprint $table) {
            $table->dropForeign(['consultant_id']);
            $table->renameColumn('consultant_id', 'constulant_id');
        });

        Schema::table('unit_constulants', function (Blueprint $table) {
            $table->foreign('constulant_id')->references('id')->on('consultants')->onDelete('cascade');
        });
    }
};
