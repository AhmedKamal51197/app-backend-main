<?php

namespace App\Actions\User;

use App\Enums\UserStatusEnum;
use App\Events\LogExceptionEvent;
use App\Models\User;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the creation a user action
 */
class CreateUserAction
{
    /**
     * Create a newly registered user.
     *
     * @throws Exception
     */
    public static function create(array $input): User
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => Arr::get($input, 'name'),
                'email' => Arr::get($input, 'email'),
                'password' => bcrypt(Arr::get($input, 'password')),
                'status' => UserStatusEnum::ACTIVE->value,
                'active' => true,
                'country_id' => Arr::get($input, 'country_id'),
                'fcm_token' => Arr::get($input, 'fcm_token')
            ]);
            $user->assignRole(Arr::get($input, 'role'));

            DB::commit();

            return $user;
        } catch (Exception $exception) {
            event(new LogExceptionEvent($exception));
            DB::rollBack();

            throw new Exception('Error while register a new user');
        }
    }
}
