<?php

use App\Models\Faq;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FAQs shown on the website: a flag to choose which questions appear there
 * and their display order. Also inserts the website's initial questions
 * (database/data/website-faqs.json) in both languages.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->boolean('show_on_landing')->default(false)->after('is_active');
            $table->unsignedInteger('sort_order')->default(0)->after('show_on_landing');
            $table->index(['show_on_landing', 'sort_order']);
        });

        $faqs = json_decode(file_get_contents(database_path('data/website-faqs.json')), true);

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(
                ['question_ar' => $faq['question_ar']],
                [
                    'question_en' => $faq['question_en'],
                    'answer_ar' => $faq['answer_ar'],
                    'answer_en' => $faq['answer_en'],
                    'type' => $faq['type'],
                    'is_active' => true,
                    'show_on_landing' => $faq['show_on_landing'],
                    'sort_order' => $faq['sort_order'],
                ]
            );
        }
    }

    public function down(): void
    {
        $questions = array_column(
            json_decode(file_get_contents(database_path('data/website-faqs.json')), true),
            'question_ar'
        );
        Faq::withTrashed()->whereIn('question_ar', $questions)->forceDelete();

        Schema::table('faqs', function (Blueprint $table) {
            $table->dropIndex(['show_on_landing', 'sort_order']);
            $table->dropColumn(['show_on_landing', 'sort_order']);
        });
    }
};
