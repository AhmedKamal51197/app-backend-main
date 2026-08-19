<?php

namespace App\Services\PayPal;

use App\Events\LogExceptionEvent;
use App\Models\BankAccount;
use App\Models\Paypal;
use App\Models\User;
use App\Notifications\Account\PayPalAddedNotification;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the PayPal
 */
class PayPalService
{
    /**
     * Get user PayPal
     *
     * @param User $user
     *
     * @return BankAccount|null
     */
    public function userPayPal(User $user): ?Paypal
    {
        return $user->paypal;
    }

    /**
     * Create or update PayPal
     *
     * @param User $user
     * @param array $data
     *
     * @return Paypal
     *
     * @throws Exception
     */
    public
    function store(User $user, array $data): Paypal
    {
        try {
            DB::beginTransaction();

            $payPal = Paypal::updateOrCreate([
                'email' => $data['email'],
                'name' => $data['name'],
                'user_name' => $data['user_name'],
                'user_id' => $user->getAttribute('id')
            ]);

            DB::commit();

            $user->notify(new PayPalAddedNotification());

            return $payPal;

        } catch (\Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
