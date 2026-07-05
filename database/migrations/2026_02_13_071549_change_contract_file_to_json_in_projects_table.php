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
        Schema::table('projects', function (Blueprint $table) {
            // Change contract_file to json or text to support multiple files
            // Using TEXT for maximum compatibility if SQLite or MySQL < 5.7
            // But Laravel's json usually maps to text on SQLite anyway.
            // Let's use json.
            $table->json('contract_file')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Revert back to string
            // Warning: This might truncate data if multiple files are stored
            $table->string('contract_file')->nullable()->change();
        });
    }
};
