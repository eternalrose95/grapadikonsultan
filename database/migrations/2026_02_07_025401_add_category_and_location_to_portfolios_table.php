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
        Schema::table('portfolios', function (Blueprint $table) {
            $table->foreignId('portfolio_category_id')
                ->nullable()
                ->after('service_id')
                ->constrained('portfolio_categories')
                ->nullOnDelete();
            $table->string('location', 100)->nullable()->after('client_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropForeign(['portfolio_category_id']);
            $table->dropColumn(['portfolio_category_id', 'location']);
        });
    }
};
