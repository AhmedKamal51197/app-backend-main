<?php

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $allowedStatuses = OrderStatusEnum::toArray();

        DB::table('orders')
            ->whereNotIn('status', $allowedStatuses)
            ->update(['status' => OrderStatusEnum::APPROVAL_PENDING->value]);

        Schema::table('orders', function (Blueprint $table) use ($allowedStatuses) {
            $orderEnumValuesString = implode("','", $allowedStatuses);
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('{$orderEnumValuesString}')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
