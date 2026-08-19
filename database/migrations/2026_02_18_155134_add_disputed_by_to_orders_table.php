<?php

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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('disputed_by', ['seeker', 'provider'])->nullable()->after('cancellation_reason');
            $table->text('disputed_reason')->nullable()->after('disputed_by');
            $table->enum('cancelled_by', ['seeker', 'provider'])->nullable()->after('disputed_reason');
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
            $table->dropColumn(['disputed_by', 'disputed_reason', 'cancelled_by']);
        });
    }
};
