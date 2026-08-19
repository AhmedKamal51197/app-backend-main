<?php

use App\Enums\LocaleDirectionEnum;
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
        Schema::create('locales', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('locale', 10)->unique();
            $table->string('language', 50);
            $table->enum('direction', LocaleDirectionEnum::toArray())->default(LocaleDirectionEnum::LTR->value);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_enabled')->default(true);
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
        Schema::dropIfExists('locales');
    }
};
