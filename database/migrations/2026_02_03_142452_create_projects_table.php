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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->enum('type', ['development', 'marketing', 'execution', 'design', 'consulting'])->default('development');
            $table->date('deadline')->nullable();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->enum('status', ['active', 'completed', 'on_hold', 'cancelled'])->default('active');
            $table->decimal('budget', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
