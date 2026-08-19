<?php

namespace App\Services\BankAccount;

use App\Events\LogExceptionEvent;
use App\Models\BankAccount;
use App\Models\User;
use App\Notifications\Account\BankAccountAddedNotification;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the user service
 */
class BankAccountService
{
    /**
     * Get user bank account
     *
     * @param User $user
     *
     * @return BankAccount|null
     */
    public function userBankAccount(User $user): ?BankAccount
    {
        return $user->bankAccount->load('country');
    }

    /**
     * Create or update bank account
     *
     * @param User $user
     * @param array $data
     *
     * @return BankAccount
     *
     * @throws Exception
     */
    public function store(User $user, array $data): BankAccount
    {
        try {
            DB::beginTransaction();

            $bankAccount = BankAccount::updateOrCreate([
                'user_name' => $data['user_name'],
                'iban' => $data['iban'],
                'swift_code' => $data['swift_code'],
                'bank_name' => $data['bank_name'],
                'bank_address' => $data['bank_address'],
                'branch_name' => $data['branch_name'],
                'user_address' => $data['user_address'],
                'country_id' => $data['country_id'],
                'user_id' => $user->getAttribute('id')
            ]);

            DB::commit();

            $user->notify(new BankAccountAddedNotification());

            return $bankAccount->load('country');

        } catch (\Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update bank account
     *
     * @param User $user
     * @param array $data
     *
     * @return BankAccount
     *
     * @throws Exception
     */
    public function update(User $user, array $data): BankAccount
    {
        try {
            DB::beginTransaction();

            $bankAccount = $user->bankAccount;

            if (!$bankAccount) {
                throw new Exception(__('User doesnt have a bank account'));
            }

            $bankAccount->update($data);

            DB::commit();

            return $bankAccount->load('country');

        } catch (\Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
