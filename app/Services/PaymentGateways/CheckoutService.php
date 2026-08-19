<?php

namespace App\Services\PaymentGateways;

use App\Enums\PaymentGatewaysEnum;
use App\Events\LogExceptionEvent;
use App\Models\PaymentGateway\PaymentGatewayCheckout;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * The service for payment
 */
class CheckoutService
{
     /**
     * Store new Neighborhood
     *
     * @param array $data
     *
     * @return PaymentGatewayCheckout
     *
     * @throws Exception
     */
    public function store(array $data): PaymentGatewayCheckout
    {
        DB::beginTransaction();
        try {
            $PaymentGatewayCheckout = PaymentGatewayCheckout::create([
                "amount"     => $data["price"],
                "payable_id"     => request()->user()->getAttribute('id'),
                "payable_type"     => User::class,
                "payment_gateway" => PaymentGatewaysEnum::MYFATOORAH->value,
            ]);

            DB::commit();

            return $PaymentGatewayCheckout;
        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
