<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * The withdrawal transfer fee is borne by the freelancer: the wallet is
     * debited by the gross `amount`, the `fee` is kept to cover the manual
     * transfer cost, and `net_amount` is what is actually sent to the bank.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->double('fee')->default(0)->after('amount');
            $table->double('net_amount')->nullable()->after('fee');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->dropColumn(['fee', 'net_amount']);
        });
    }
};
