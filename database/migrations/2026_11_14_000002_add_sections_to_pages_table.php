<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bilingual, structured legal pages: title/summary/version per page and an
 * ordered list of sections (heading + body in Arabic and English).
 * `pages.content` stays for app versions that still read one block of text.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('title_ar', 150)->nullable()->after('key');
            $table->string('title_en', 150)->nullable()->after('title_ar');
            $table->string('summary_ar', 400)->nullable()->after('title_en');
            $table->string('summary_en', 400)->nullable()->after('summary_ar');
            $table->string('version', 20)->default('1.0')->after('summary_en');
            $table->timestamp('published_at')->nullable()->after('version');
            $table->boolean('en_enabled')->default(false)->after('published_at');
        });

        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->string('slug', 60);
            $table->unsignedInteger('sort_order');
            $table->string('heading_ar', 150);
            $table->string('heading_en', 150)->nullable();
            $table->text('body_ar');
            $table->text('body_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['page_id', 'slug']);
            $table->index(['page_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_sections');

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['title_ar', 'title_en', 'summary_ar', 'summary_en', 'version', 'published_at', 'en_enabled']);
        });
    }
};
