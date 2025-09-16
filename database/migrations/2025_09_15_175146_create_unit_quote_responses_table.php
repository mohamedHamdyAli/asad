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
        Schema::create('unit_quote_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_quote_id')->constrained('unit_quotes')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('time_line')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_quote_responses');
    }
};
