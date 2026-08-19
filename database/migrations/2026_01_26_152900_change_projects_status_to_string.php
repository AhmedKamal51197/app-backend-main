<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('draft', 'pending', 'in_progress', 'completed', 'disputed', 'refunded', 'cancelled', 'cancel_pending') NOT NULL DEFAULT 'draft'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('draft', 'pending', 'in_progress', 'completed', 'disputed', 'refunded', 'cancelled') NOT NULL DEFAULT 'draft'");
    }
};