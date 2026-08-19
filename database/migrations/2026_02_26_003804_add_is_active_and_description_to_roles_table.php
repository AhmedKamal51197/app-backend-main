<?php

use App\Enums\RoleAccessLevelEnum;
use App\Enums\RoleTypeEnum;
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
        Schema::table('roles', function (Blueprint $table) {
            $table->uuid();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->enum('type', RoleTypeEnum::toArray());
            $table->enum('access_level', RoleAccessLevelEnum::toArray());
        });

           DB::table('roles')->whereNull('uuid')->orWhere('uuid', '')->get()->each(function($role) {
            DB::table('roles')->where('id', $role->id)->update(['uuid' => Str::uuid()]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'is_active', 'description', 'type', 'access_level']);
        });
    }
};
