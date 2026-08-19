<?php

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->foreignIdFor(Category::class)->nullable()->after('enable_price')->constrained();
            $table->foreignIdFor(SubCategory::class)->nullable()->after('category_id')->constrained();
            $table->boolean('hidden')->default(false)->after('sub_category_id');
            $table->dropForeign(['industry_id']);
            $table->dropForeign(['duration_id']);
            $table->dropColumn(['industry_id', 'duration_id', 'is_enabled', 'price', 'enable_price']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Category::class);
            $table->dropConstrainedForeignIdFor(SubCategory::class);
            $table->dropColumn('hidden');
            $table->foreignId('industry_id')->nullable()->constrained();
            $table->foreignId('duration_id')->nullable()->constrained();
            $table->boolean('is_enabled')->default(true);
            $table->float('price')->nullable();
            $table->boolean('enable_price')->default(false);
        });
    }
};
