<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adding order_id to chats table to directly link chats to orders.
     * This is nullable because:
     * 1. Chats can be created before an order is placed (for service discussions)
     * 2. Not all chat types have orders (user, team, project chats)
     * 3. When an order is created from a service chat, we can update the order_id
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->foreignIdFor(Order::class)->nullable()->after('chattable_type')->constrained()->nullOnDelete();
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropIndex(['order_id']);
            $table->dropColumn('order_id');
        });
    }
};
