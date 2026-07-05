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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            
            // Company Information
            $table->string('company_name', 200);
            $table->string('industry', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('website', 255)->nullable();
            
            // Primary Contact (PIC)
            $table->string('pic_name', 100);
            $table->string('pic_phone', 20)->nullable();
            $table->string('pic_email', 100)->nullable();
            $table->string('pic_position', 100)->nullable();
            
            // Business Info
            $table->decimal('contract_value', 15, 2)->nullable();
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->enum('status', ['active', 'inactive', 'churned'])->default('active');
            
            // Tracking
            $table->foreignId('converted_from_lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
