<?php

namespace App\Services\Checkout;

use App\Enums\PaymentGatewaysEnum;
use App\Events\LogExceptionEvent;
use App\Models\CommissionSetting;
use App\Models\PaymentGateway\PaymentGatewayCheckout;
use App\Models\ServicePackage;
use App\Models\User;
use App\Services\Payment\MyFatoorahService;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * The service for checkout
 */
class CheckoutService
{
    /**
     * Load the checkout service
     *
     * @param MyFatoorahService $myFatoorahService
     */
    public function __construct(protected MyFatoorahService $myFatoorahService)
    {
    }

    /**
     * Store new checkout
     *
     * @param ServicePackage $package
     * @param User $user
     * @param array $data
     *
     * @return array
     *
     * @throws Exception
     */
    public function checkout(ServicePackage $package, User $user, array $data): array
    {
        DB::beginTransaction();
        try {

            $commissionSetting = CommissionSetting::where([
                'key' => 'seeker',
            ])->first();

            $checkout = PaymentGatewayCheckout::create([
                'amount' => $package->getAttribute('price') + ($package->getAttribute('price') * $commissionSetting->value),
                'payable_id' => $package->getAttribute('id'),
                'payable_type' => ServicePackage::class,
                'user_id' => $user->getAttribute('id'),
                "payment_gateway" => PaymentGatewaysEnum::MYFATOORAH->value,
            ]);

            $myFatoorahCheckout = $this->myFatoorahService->checkout(request()->user(), $checkout, $data['payment_method_id']);

            DB::commit();

            return $myFatoorahCheckout;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
