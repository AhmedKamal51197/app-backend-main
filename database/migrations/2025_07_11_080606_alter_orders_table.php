<?php

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
        Schema::table('orders', function (Blueprint $table) {
            $table->dateTime('rejected_at')->after('approved_at')->nullable();
            $table->dateTime('revision_at')->after('approved_at')->nullable();
            $table->dateTime('cancel_requested_at')->after('approved_at')->nullable();
            $table->dateTime('release_requested_at')->after('approved_at')->nullable();
            $table->dateTime('refunded_at')->after('approved_at')->nullable();
            $table->dateTime('disputed_at')->after('approved_at')->nullable();
            $table->dateTime('completed_at')->after('approved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['rejected_at', 'revision_at', 'cancel_requested_at', 'release_requested_at',
                'refunded_at', 'disputed_at', 'completed_at'
                ]);
        });
    }
};
