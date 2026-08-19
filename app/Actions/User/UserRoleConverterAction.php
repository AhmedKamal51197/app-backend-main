<?php

namespace App\Actions\User;

use App\Enums\UserTypesEnum;
use App\Events\LogExceptionEvent;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Arr;

/**
 * A class defines the get user role
 */
class UserRoleConverterAction
{
    /**
     * Create a newly registered user.
     *
     * @throws Exception
     */
    public static function parse(string $role): int
    {
        try {
            if (!(in_array($role, array_column(UserTypesEnum::cases(), 'value')))) {
                throw new Exception('Error while convert role');
            }

            return Role::where('name', $role)->first()->id;
        } catch (Exception $exception) {
            event(new LogExceptionEvent($exception));

            throw new Exception(__('Error while convert role'));
        }
    }
}
