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
        Schema::create('payment_gateway_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->json('request_body')->nullable();
            $table->json('request_header')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->enum('payment_gateway', PaymentGatewaysEnum::toArray())->nullable();
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
        Schema::dropIfExists('payment_gateway_webhook_logs');
    }
};
