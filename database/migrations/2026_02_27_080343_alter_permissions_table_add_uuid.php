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
        Schema::table('permissions', function (Blueprint $table) {
            $table->uuid();
        });

        // Generate UUIDs for existing permissions
        DB::table('permissions')->whereNull('uuid')->orWhere('uuid', '')->get()->each(function($perm) {
            DB::table('permissions')->where('id', $perm->id)->update(['uuid' => Str::uuid()]);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
