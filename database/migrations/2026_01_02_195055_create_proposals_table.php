<?php

use App\Enums\ProposalStatusEnum;
use App\Models\Project;
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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->text('description');
            $table->enum('status', ProposalStatusEnum::toArray())->default(ProposalStatusEnum::PENDING->value);
            $table->integer('time');
            $table->decimal('price', 10, 2);
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Project::class)->constrained();
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
        Schema::dropIfExists('proposals');
    }
};
