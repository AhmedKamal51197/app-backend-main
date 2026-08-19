<?php

use App\Enums\RefundRequestStatusEnum;
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
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->morphs('refundable');
            $table->date('released_at')->nullable();
            $table->float('amount');
            $table->string('reason')->nullable();
            $table->enum('status', RefundRequestStatusEnum::toArray())->default(RefundRequestStatusEnum::PENDING->value);
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
        Schema::dropIfExists('refund_requests');
    }
};
