<?php

use App\Enums\PaymentRequestStatusEnum;
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
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->double('amount');
            $table->string('notes')->nullable();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(User::class, 'payer_id')->nullable();
            $table->enum('status', PaymentRequestStatusEnum::toArray())->default(PaymentRequestStatusEnum::PENDING->value);
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
        Schema::dropIfExists('payment_requests');
    }
};
