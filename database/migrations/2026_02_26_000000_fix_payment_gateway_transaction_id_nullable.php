<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the foreign key constraint
        DB::statement('ALTER TABLE receipts DROP FOREIGN KEY receipts_payment_gateway_transaction_id_foreign');
        
        // Make the column nullable
        DB::statement('ALTER TABLE receipts MODIFY payment_gateway_transaction_id BIGINT UNSIGNED NULL');
        
        // Re-add the foreign key constraint
        DB::statement('ALTER TABLE receipts ADD CONSTRAINT receipts_payment_gateway_transaction_id_foreign FOREIGN KEY (payment_gateway_transaction_id) REFERENCES payment_gateway_transactions(id)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the foreign key constraint
        DB::statement('ALTER TABLE receipts DROP FOREIGN KEY receipts_payment_gateway_transaction_id_foreign');
        
        // Make the column NOT NULL
        DB::statement('ALTER TABLE receipts MODIFY payment_gateway_transaction_id BIGINT UNSIGNED NOT NULL');
        
        // Re-add the foreign key constraint
        DB::statement('ALTER TABLE receipts ADD CONSTRAINT receipts_payment_gateway_transaction_id_foreign FOREIGN KEY (payment_gateway_transaction_id) REFERENCES payment_gateway_transactions(id)');
    }
};
