<?php

use App\Enums\CertificateLevelEnum;
use App\Models\CertificateProvider;
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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignIdFor(CertificateProvider::class)->constrained();
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('level', CertificateLevelEnum::toArray())->default(CertificateLevelEnum::BEGINNER->value);
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('certificates');
    }
};
