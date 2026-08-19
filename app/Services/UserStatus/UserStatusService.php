<?php

namespace App\Services\UserStatus;

use App\Events\LogExceptionEvent;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the user status service
 */
class UserStatusService
{
    /**
     * Change user status
     *
     * @param User $user
     * @param array $data
     *
     * @return User
     *
     * @throws Exception
     */
    public function changeStatus(User $user, array $data): User
    {
        DB::beginTransaction();
        try {
            $user->update([
                'status' => $data['status'],
            ]);

            DB::commit();

            return $user;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
