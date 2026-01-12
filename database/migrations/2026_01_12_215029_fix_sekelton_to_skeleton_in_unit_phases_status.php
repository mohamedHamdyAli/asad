<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        Schema::table('unit_phases', function (Blueprint $table) {
            $table->enum('status', [
                'site_handover',
                'skeleton',
                'finishing',
                'handover'
            ])->default('site_handover')->change();
        });
    }

    public function down(): void
    {

        Schema::table('unit_phases', function (Blueprint $table) {
            $table->enum('status', [
                'site_handover',
                'sekelton',
                'finishing',
                'handover'
            ])->default('site_handover')->change();
        });
    }
};
