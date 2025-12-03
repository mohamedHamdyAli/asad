<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('consultants', function (Blueprint $table) {
            $table->string('company_address')->nullable()->after('image');
            $table->string('company_phone')->nullable()->after('company_address');
            $table->string('representative_name')->nullable()->after('company_phone');
            $table->string('representative_phone')->nullable()->after('representative_name');
        });
    }


    public function down(): void
    {
        Schema::table('consultants', function (Blueprint $table) {
            $table->dropColumn([
                'company_address',
                'company_phone',
                'representative_name',
                'representative_phone',
            ]);
        });
    }
};
