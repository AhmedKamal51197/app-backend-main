<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to modify the enum column
        DB::statement("ALTER TABLE attachments MODIFY COLUMN document_type ENUM(
            'identity_card_front',
            'identity_card_back', 
            'user_holding_id',
            'identity_card',
            'passport',
            'video',
            'picture',
            'document',
            'portfolio',
            'photo',
            'logo',
            'certificate',
            'signature',
            'contract'
        ) NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE attachments MODIFY COLUMN document_type ENUM(
            'identity_card',
            'passport',
            'video',
            'picture',
            'document',
            'portfolio',
            'photo',
            'logo',
            'certificate',
            'signature',
            'contract'
        ) NULL");
    }
};