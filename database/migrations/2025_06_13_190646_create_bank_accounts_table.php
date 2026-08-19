<?php

use App\Models\Country;
use App\Models\User;
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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('user_name');
            $table->string('iban');
            $table->string('swift_code');
            $table->string('bank_name');
            $table->string('bank_address');
            $table->string('branch_name');
            $table->string('user_address');
            $table->foreignIdFor(Country::class)->constrained();
            $table->foreignIdFor(User::class)->unique()->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_accounts');
    }
};
