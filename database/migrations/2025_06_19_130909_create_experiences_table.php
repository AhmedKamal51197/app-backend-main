<?php

use App\Enums\EmploymentTypeEnum;
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
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('title');
            $table->string('company');
            $table->string('description')->nullable();
            $table->enum('employment_type', EmploymentTypeEnum::toArray())->default(EmploymentTypeEnum::FULL_TIME->value);
            $table->date('start_date');
            $table->boolean('is_current');
            $table->date('end_date')->nullable();
            $table->foreignIdFor(User::class)->constrained();
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
        Schema::dropIfExists('experiences');
    }
};
