<?php

use App\Enums\UserTypeEnum;
use App\Models\User;
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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->nullableMorphs('rateable');
            $table->foreignIdFor(User::class, 'rater_id')->constrained('users');
            $table->foreignIdFor(User::class, 'rated_user_id')->constrained('users');
            $table->float('rate')->check('rate >= 1 and rate <= 5');
            $table->longText('comment')->nullable();
            $table->enum('rater_type', UserTypeEnum::toArray());
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
        Schema::dropIfExists('rates');
    }
};
