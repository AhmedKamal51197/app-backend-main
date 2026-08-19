<?php

use App\Enums\BalanceStatusEnum;
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
        Schema::create('pending_balances', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignIdFor(User::class)->constrained();
            $table->double('balance');
            $table->string('reason_ar')->nullable();
            $table->string('reason_en')->nullable();
            $table->enum('status', BalanceStatusEnum::toArray())->default(BalanceStatusEnum::PENDING->value);
            $table->dateTime('release_at')->nullable();
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
        Schema::dropIfExists('pending_balances');
    }
};
