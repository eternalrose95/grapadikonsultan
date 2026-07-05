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
        Schema::table('articles', function (Blueprint $table) {
            $table->text('excerpt')->nullable()->after('content');
            $table->integer('reading_time')->nullable()->after('excerpt');
            $table->boolean('is_featured')->default(false)->after('reading_time');
            $table->boolean('is_published')->default(true)->after('is_featured');
            $table->timestamp('published_at')->nullable()->after('is_published');
            $table->string('meta_title', 255)->nullable()->after('published_at');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->unsignedInteger('views_count')->default(0)->after('meta_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'excerpt',
                'reading_time',
                'is_featured',
                'is_published',
                'published_at',
                'meta_title',
                'meta_description',
                'views_count',
            ]);
        });
    }
};
