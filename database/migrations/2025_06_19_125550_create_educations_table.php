<?php

use App\Models\EducationDegree;
use App\Models\EducationMajor;
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
        Schema::create('educations', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('title');
            $table->year('graduation_year')->nullable();
            $table->longText('description')->nullable();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(EducationDegree::class)->constrained();
            $table->foreignIdFor(EducationMajor::class)->constrained();
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
        Schema::dropIfExists('educations');
    }
};
