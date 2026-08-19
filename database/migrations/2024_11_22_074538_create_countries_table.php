<?php

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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('iso')->nullable();
            $table->string('iso3')->nullable();
            $table->string('english_name');
            $table->string('arabic_name');
            $table->string('phone_code');
            $table->string('currency')->nullable();
            $table->string('currency_code')->nullable();
            $table->string('capital')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
};
