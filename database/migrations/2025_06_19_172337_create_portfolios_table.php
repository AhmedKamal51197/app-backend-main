<?php

use App\Models\Duration;
use App\Models\Industry;
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
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('title');
            $table->longText('description');
            $table->boolean('is_enabled')->default(1);
            $table->float('price')->nullable();
            $table->boolean('enable_price')->default(0);
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Duration::class)->constrained();
            $table->foreignIdFor(Industry::class)->constrained();
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
        Schema::dropIfExists('portfolios');
    }
};
