<?php

use App\Enums\PaymentGatewaysEnum;
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
        Schema::create('payment_gateway_checkouts', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->json('request_body')->nullable();
            $table->json('request_header')->nullable();
            $table->morphs('payable');
            $table->boolean('is_processed')->default(false);
            $table->enum('payment_gateway', PaymentGatewaysEnum::toArray())->nullable();
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
        Schema::dropIfExists('payment_gateway_checkouts');
    }
};
