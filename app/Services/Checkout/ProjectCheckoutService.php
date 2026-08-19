<?php

namespace App\Services\Checkout;

use App\Enums\PaymentGatewaysEnum;
use App\Events\LogExceptionEvent;
use App\Models\CommissionSetting;
use App\Models\PaymentGateway\PaymentGatewayCheckout;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use App\Services\Payment\MyFatoorahService;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * The service for proposal checkout
 */
class ProjectCheckoutService
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
     * Store new proposal checkout
     *
     * @param Proposal $proposal
     * @param User $user
     * @param array $data
     *
     * @return array
     *
     * @throws Exception
     */
    public function checkout(Project $project, User $user, array $data): array
    {
        DB::beginTransaction();
        try {
            $commissionSetting = CommissionSetting::where('key', 'seeker')->first();

            $proposal =  $project->selectedProposal;

            $checkout = PaymentGatewayCheckout::create([
                'amount' => $proposal->price + ($proposal->price * $commissionSetting->value),
                'payable_id' => $project->id,
                'payable_type' => Project::class,
                'user_id' => $user->id,
                'payment_gateway' => PaymentGatewaysEnum::MYFATOORAH->value,
            ]);

            $myFatoorahCheckout = $this->myFatoorahService->checkout($user, $checkout, $data['payment_method_id']);

            DB::commit();

            return $myFatoorahCheckout;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
