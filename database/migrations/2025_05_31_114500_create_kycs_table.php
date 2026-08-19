<?php

use App\Enums\KycStatusEnum;
use App\Models\User;
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
        Schema::create('kycs', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('name');
            $table->foreignIdFor(User::class)->constrained();
            $table->enum('status', KycStatusEnum::toArray())->default(KycStatusEnum::PENDING->value);
            $table->date('birth_date');
            $table->string('address_1');
            $table->string('address_2');
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
        Schema::dropIfExists('kycs');
    }
};
