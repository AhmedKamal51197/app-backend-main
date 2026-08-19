<?php

namespace App\Services\User;


use App\Actions\Attachments\StoreAttachmentAction;
use App\Actions\Files\GuessFileTypeAction;
use App\Enums\AttachmentDocumentTypeEnum;
use App\Enums\AttachmentStorageEnum;
use App\Enums\KycStatusEnum;
use App\Enums\ServiceTypeEnum;
use App\Enums\UserStatusEnum;
use App\Events\LogExceptionEvent;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Role;
use App\Models\Service;
use App\Models\Setting;
use App\Models\SubCategory;
use App\Models\User;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * A class defines the user service
 */
class UserService
{
    /**
     * Define the function to change profile photo
     *
     * @param User $user
     * @param array $data
     *
     * @return User
     *
     * @throws Exception
     */
    public function changeProfilePhoto(User $user, array $data): User
    {
        StoreAttachmentAction::store($user, $data['avatar'], 'avatar', true);

        return $user->load('avatar');
    }

    /**
     * Get all notifications
     *
     * @param User $user
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function notifications(User $user, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        $notifications = Notification::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        Notification::query()
            ->where('user_id', $user->id)
            ->where('seen', false)
            ->update(['seen' => true]);

        return $notifications;
    }

    /**
     * Define the function to edit profile
     *
     * @param User $user
     * @param array $data
     *
     * @return User
     *
     * @throws Exception
     */
    public function editProfile(User $user, array $data): User
    {
        DB::beginTransaction();
        try {
            $user->update([
                'name' => $data['name'] ?? $user->getAttribute('name'),
                'about' => $data['about'] ?? $user->getAttribute('about'),
                'username' => $data['username'] ?? $user->getAttribute('username'),
            ]);

            if (!empty($data['avatar'])) {
                StoreAttachmentAction::store($user, $data['avatar'], 'avatar', true);
            }

            DB::commit();

            return $user->load('avatar');

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Define the function to edit user category
     *
     * @param User $user
     * @param array $data
     *
     * @return User
     *
     * @throws Exception
     */
    public function editCategory(User $user, array $data): User
    {
        DB::beginTransaction();
        try {
            $categoryId = null;
            if (isset($data['category_id'])) {
                $categoryId = Category::where('uuid', $data['category_id'])->value('id');
            }

            $subCategoryId = null;
            if (isset($data['sub_category_id'])) {
                $subCategoryId = SubCategory::where('uuid', $data['sub_category_id'])->value('id');
            }

            $user->update([
                'category_id' => $categoryId ?? $user->category_id,
                'sub_category_id' => $subCategoryId ?? $user->sub_category_id,
            ]);

            DB::commit();

            return $user->load(['category', 'subCategory']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * User home request
     *
     * @param int $perPage
     *
     * @return array
     */
    public function home(int $perPage = Setting::PAGE_RESULT_LIMIT): array
    {
        $providerRoleId = Role::where('name', 'provider')->value('id');

        $categories = Category::query()
            ->where('is_enabled', '=', true)
            ->with('image')
            ->paginate($perPage);

        $subCategories = SubCategory::query()
            ->where('is_enabled', '=', true)
            ->with('image')
            ->paginate($perPage);

        $services = Service::query()
//            ->where('is_enabled', '=', true)
            ->with(['attachments', 'packages'])
//            ->where('hidden', '=', false)
//            ->where('custom_offer', '=', false)
            ->with('user')
            ->where('type', '=', ServiceTypeEnum::ONE_TIME->value)
            ->whereHas('user', function ($query) {
                $query->where('status', UserStatusEnum::ACTIVE->value)
                    ->whereHas('kycs', function ($query) {
                        $query->where('status', KycStatusEnum::APPROVED->value);
                    });
            })
            ->paginate($perPage);

        $partTimeServices = Service::query()
            ->where('is_enabled', '=', true)
            ->with(['attachments', 'packages'])
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('status', UserStatusEnum::ACTIVE->value)
                    ->whereHas('kycs', function ($query) {
                        $query->where('status', KycStatusEnum::APPROVED->value);
                    });
            })
            ->where('hidden', '=', false)
            ->where('type', '=', ServiceTypeEnum::PART_TIME->value)
            ->paginate($perPage);

        $freelancers = User::query()
            ->whereHas('roles', function ($query) use ($providerRoleId) {
                $query->where('id', $providerRoleId);
            })
            ->where('status', '=', UserStatusEnum::ACTIVE->value)
            ->with('category')
            ->whereHas('kycs', function ($query) {
                $query->where('status', KycStatusEnum::APPROVED->value);
            })
            ->paginate($perPage);

        return [
            'categories' => $categories,
            'sub_categories' => $subCategories,
            'services' => $services,
            'part_time_services' => $partTimeServices,
            'freelancers' => $freelancers,
        ];
    }

    /**
     * Change User Password
     *
     * @param User $user
     * @param array $data
     *
     * @return User
     */
    public function changePassword(User $user, array $data): User
    {
        $user->update([
            'password' => bcrypt($data['password']),
        ]);

        return $user;
    }

    /**
     * Send custom push notification
     *
     * @param User $actor
     * @param array $data
     *
     * @return void
     */
    public function sendCustomNotification(User $actor, array $data): void
    {
        Notification::create([
            'user_id' => $actor->id,
            'title' => $data['title'],
            'body' => $data['body'],
            'seen' => false,
            'important' => false,
            'reference' => \App\Enums\NotificationReferenceEnum::SYSTEM->value,
        ]);

        $actor->notify(new \App\Notifications\User\CustomPushNotification($data['title'], $data['body']));
    }
}
