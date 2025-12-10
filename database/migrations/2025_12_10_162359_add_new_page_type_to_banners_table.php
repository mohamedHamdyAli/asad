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
        Schema::table('banners', function (Blueprint $table) {
            $table->enum('page', ['home', 'about', 'contactUs','project','application','logos','our-services','qhse-policy'])->default('application')->after('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
             $table->enum('page', ['home', 'about', 'contactUs','project','application','logos'])->default('application')->after('description')->nullable()->change();
        });
    }
};
