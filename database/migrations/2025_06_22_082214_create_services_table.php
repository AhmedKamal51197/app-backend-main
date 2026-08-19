<?php

use App\Enums\ServiceTypeEnum;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('title');
            $table->longText('description');
            $table->foreignIdFor(Category::class)->constrained();
            $table->foreignIdFor(SubCategory::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
            $table->enum('type', ServiceTypeEnum::toArray())->default(ServiceTypeEnum::ONE_TIME->value);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
};
