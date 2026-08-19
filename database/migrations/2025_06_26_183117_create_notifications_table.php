<?php

use App\Enums\NotificationReferenceEnum;
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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('title');
            $table->text('body');
            $table->boolean('seen')->default(false);
            $table->enum('reference', NotificationReferenceEnum::toArray())->default(NotificationReferenceEnum::SYSTEM->value);
            $table->string('reference_id')->nullable();
            $table->foreignIdFor(User::class)->constrained();
            $table->boolean('important')->default(false);
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
        Schema::dropIfExists('notifications');
    }
};
