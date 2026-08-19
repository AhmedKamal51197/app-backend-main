<?php

namespace App\Services\UserSubCategory;

use App\Events\LogExceptionEvent;
use App\Models\SubCategory;
use App\Models\SubCategoryUser;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the user sub category service
 */
class UserSubCategoryService
{
    /**
     * Store user sub-categories without detaching the old ones.
     *
     * @param User $user
     * @param array $data
     *
     * @return User
     *
     * @throws Exception
     */
    public function storeUserSubCategories(User $user, array $data): User
    {
        DB::beginTransaction();
        try {
            $subCategories = SubCategory::whereIn('uuid', $data['sub_category_ids'])->get();

            foreach ($subCategories as $subCategory) {
                $alreadyAttached = $user->userSubCategories()
                    ->where('sub_category_id', $subCategory->id)
                    ->exists();

                if (!$alreadyAttached) {
                    SubCategoryUser::create([
                        'sub_category_id' => $subCategory->id,
                        'user_id' => $user->getAttribute('id')
                    ]);
                }
            }

            $user->load(['userSubCategories', 'userSubCategories.subCategory']);

            DB::commit();

            return $user;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Remove user sub categories
     *
     * @param User $user
     * @param array $data
     *
     * @return User
     *
     * @throws Exception
     */
    public function removeUserSubCategories(User $user, array $data): User
    {
        DB::beginTransaction();
        try {
            $subCategories = SubCategory::whereIn('uuid', $data['sub_category_ids'])->get();
            $subCategoriesIds = $subCategories->pluck('id')->toArray();

            $user->userSubCategories()
                ->whereIn('sub_category_id', $subCategoriesIds)
                ->delete();

            $user->load(['userSubCategories', 'userSubCategories.subCategory']);

            DB::commit();

            return $user;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
