<?php

use App\Enums\PaymentGatewaysEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The enum columns that list the payment gateways, with their nullability
     *
     * @var array<string, string>
     */
    private array $enumColumns = [
        'payment_gateway_transactions' => 'NOT NULL',
        'payment_gateway_checkouts' => 'NULL',
        'payment_gateway_webhook_logs' => 'NULL',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->setGatewayEnum(PaymentGatewaysEnum::toArray());

        Schema::table('payment_gateway_checkouts', function (Blueprint $table) {
            $table->string('gateway_reference')->nullable()->unique();
            $table->string('gateway_status')->nullable();
            $table->char('currency', 3)->nullable();
            $table->timestamp('paid_at')->nullable();
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->string('status')->nullable()->index();
            $table->string('gateway_reference')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->dropUnique(['gateway_reference']);
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'gateway_reference']);
        });

        Schema::table('payment_gateway_checkouts', function (Blueprint $table) {
            $table->dropUnique(['gateway_reference']);
            $table->dropColumn(['gateway_reference', 'gateway_status', 'currency', 'paid_at']);
        });

        // Fails if Tap rows still exist, which is the safe outcome.
        $this->setGatewayEnum([PaymentGatewaysEnum::MYFATOORAH->value]);
    }

    /**
     * MySQL enums must be redefined to accept a new value.
     *
     * @param array $values
     *
     * @return void
     */
    private function setGatewayEnum(array $values): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $list = implode(',', array_map(fn (string $value) => DB::getPdo()->quote($value), $values));

        foreach ($this->enumColumns as $table => $nullable) {
            DB::statement("ALTER TABLE `{$table}` MODIFY `payment_gateway` ENUM({$list}) {$nullable}");
        }
    }
};
