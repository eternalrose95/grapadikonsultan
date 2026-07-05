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
        Schema::create('company_timelines', function (Blueprint $table) {
            $table->id();
            $table->string('year', 10);  // e.g., "2010", "2024"
            $table->string('title', 200);
            $table->text('description');
            $table->string('image')->nullable(); // Optional image
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_timelines');
    }
};
