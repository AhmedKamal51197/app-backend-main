<?php

use App\Enums\MessageTypeEnum;
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
        $allowedStatuses = MessageTypeEnum::toArray();

        DB::table('messages')
            ->whereNotIn('type', $allowedStatuses)
            ->update(['type' => MessageTypeEnum::TEXT->value]);

        Schema::table('messages', function (Blueprint $table) use ($allowedStatuses) {
            $messageTypeEnumValuesString = implode("','", $allowedStatuses);
            DB::statement("ALTER TABLE messages MODIFY COLUMN type ENUM('{$messageTypeEnumValuesString}')");
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
