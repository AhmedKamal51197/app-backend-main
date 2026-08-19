<?php

namespace App\Services\UserCategory;

use App\Events\LogExceptionEvent;
use App\Models\Category;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the user category service
 */
class UserCategoryService
{
    /**
     * Adding the store user category
     *
     * @param User $user
     * @param array $data
     *
     * @return User
     *
     * @throws Exception
     */
    public function storeUserCategory(User $user, array $data) : User
    {
        DB::beginTransaction();
        try {
            $category = Category::where('uuid', $data['category_id'])->firstOrFail();

            $user->update([
                'category_id' => $category->id,
            ]);

            $user->load(['category']);

            DB::commit();

            return $user;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
