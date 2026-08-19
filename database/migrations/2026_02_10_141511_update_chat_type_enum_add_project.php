<?php

use App\Enums\ChatTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        $allowedTypes = ChatTypeEnum::toArray();

        Schema::table('chats', function (Blueprint $table) use ($allowedTypes) {
            $chatTypeEnumValuesString = implode("','", $allowedTypes);
            DB::statement("ALTER TABLE chats MODIFY COLUMN type ENUM('{$chatTypeEnumValuesString}')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            DB::statement("ALTER TABLE chats MODIFY COLUMN type ENUM('user','team','service','job')");
        });
    }
};
