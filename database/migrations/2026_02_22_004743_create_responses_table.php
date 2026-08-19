<?php

use App\Enums\MessageTypeEnum;
use App\Models\Report;
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
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignIdFor(Report::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'sender_id')->constrained('users');
            $table->text('content')->nullable();
            $table->enum('type', MessageTypeEnum::toArray())->default(MessageTypeEnum::TEXT->value);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['report_id', 'created_at']);
            $table->index(['sender_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('responses');
    }
};
