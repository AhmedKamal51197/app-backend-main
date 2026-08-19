<?php

use App\Models\Country;
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
        Schema::table('kycs', function (Blueprint $table) {
            $table->dropColumn(['name', 'address_1', 'address_2']);
            $table->string('first_name')->after('uuid');
            $table->string('last_name')->after('first_name');
            $table->foreignIdFor(Country::class)->after('birth_date')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kycs', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn(['first_name', 'last_name', 'country_id']);
            $table->string('name')->after('uuid');
            $table->string('address_1')->after('birth_date');
            $table->string('address_2')->after('address_1');
        });
    }
};
