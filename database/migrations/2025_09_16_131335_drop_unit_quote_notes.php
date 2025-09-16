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
        Schema::dropIfExists('unit_quote_notes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('unit_quote_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->enum('status', ['open', 'close'])->default('open');
            $table->timestamps();
        });
    }
};
