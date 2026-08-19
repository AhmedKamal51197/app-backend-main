<?php

use App\Enums\OrderStatusEnum;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->morphs('orderable');
            $table->float('price');
            $table->float('commissions');
            $table->string('code');
            $table->integer('time');
            $table->enum('status', OrderStatusEnum::toArray())->default(OrderStatusEnum::APPROVAL_PENDING->value);
            $table->foreignIdFor(User::class, 'seeker_id')->constrained('users');
            $table->foreignIdFor(User::class, 'provider_id')->constrained('users');
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('released_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
