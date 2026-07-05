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
        // Add client_id to projects
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // Add converted_to_client_id to leads
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('converted_to_client_id')->nullable()->after('source')->constrained('clients')->nullOnDelete();
            $table->dateTime('converted_at')->nullable()->after('converted_to_client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('converted_to_client_id');
            $table->dropColumn('converted_at');
        });
    }
};
