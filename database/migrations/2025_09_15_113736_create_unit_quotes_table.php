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
        Schema::create('unit_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('other_title')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('type_of_building_id')->constrained('type_of_buildings')->onDelete('cascade');
            $table->foreignId('type_of_price_id')->constrained('type_of_prices')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_quotes');
    }
};
