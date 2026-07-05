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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relation (can be lead or client)
            $table->morphs('interactable'); // interactable_type, interactable_id
            
            // Interaction details
            $table->enum('type', ['call', 'whatsapp', 'email', 'meeting', 'video_call', 'other'])->default('whatsapp');
            $table->string('subject', 200)->nullable();
            $table->text('notes');
            $table->enum('outcome', ['positive', 'neutral', 'negative', 'no_response'])->nullable();
            
            // Follow-up
            $table->dateTime('follow_up_at')->nullable();
            $table->boolean('follow_up_completed')->default(false);
            
            // User tracking
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
