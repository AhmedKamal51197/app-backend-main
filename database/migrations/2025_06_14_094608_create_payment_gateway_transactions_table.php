<?php

use App\Enums\PaymentGatewaysEnum;
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
        Schema::create('payment_gateway_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('transaction_id');
            $table->json('transaction_response')->nullable();
            $table->float('amount');
            $table->enum('payment_gateway', PaymentGatewaysEnum::toArray());
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
        Schema::dropIfExists('payment_gateway_transactions');
    }
};
