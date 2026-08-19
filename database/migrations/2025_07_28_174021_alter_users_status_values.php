<?php

use App\Enums\UserStatusEnum;
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
        $allowedStatuses = UserStatusEnum::toArray();

        DB::table('users')
            ->whereNotIn('status', $allowedStatuses)
            ->update(['status' => UserStatusEnum::ACTIVE->value]);

        Schema::table('users', function (Blueprint $table) use ($allowedStatuses) {
            $userStatusEnumValuesString = implode("','", $allowedStatuses);
            DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('{$userStatusEnumValuesString}')");
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
