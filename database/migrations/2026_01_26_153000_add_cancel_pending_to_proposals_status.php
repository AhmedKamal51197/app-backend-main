<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE proposals MODIFY COLUMN status ENUM('pending', 'accepted', 'rejected', 'in_progress', 'completed', 'cancelled', 'cancel_pending') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE proposals MODIFY COLUMN status ENUM('pending', 'accepted', 'rejected', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};