<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\User\ChangeAvatarRequest;
use App\Http\Requests\Api\User\ChangePasswordRequest;
use App\Http\Requests\Api\User\EditProfileRequest;
use App\Http\Requests\Api\User\EditUserCategoryRequest;
use App\Http\Requests\Api\Wallet\StoreWalletRequest;
use App\Http\Resources\Api\Home\HomeResource;
use App\Http\Resources\Api\Notification\NotificationResource;
use App\Http\Resources\Api\User\UserResource;
use App\Models\Setting;
use App\Services\User\UserService;
use App\Services\Wallet\WalletService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * A class defines the user controller
 */
class UserController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param UserService $service
     */
    public function __construct(protected UserService $service)
    {
    }

    /**
     * Get User Information
     *
     * @return JsonResponse
     */
    public function profile(): JsonResponse
    {
        $user = request()->user();

        $user->load([
            'category',
            'subCategory',
            'country',
            'userSubCategories', 'userSubCategories.subCategory',
            'userSkills', 'userSkills.skill',
            'userCertificates', 'userCertificates.certificate',
            'avatar',
            'bankAccount',
            'paypal',
            'educations', 'educations.educationDegree', 'educations.image',
            'portfolios', 'portfolios.attachments',
            'services', 'services.attachments'
        ]);

        return $this->jsonSuccess(UserResource::make($user));
    }

    /**
     * Notifications Request
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function notifications(Request $request): JsonResponse
    {
        $notifications = $this->service->notifications(request()->user(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(NotificationResource::collection($notifications));
    }

    /**
     * Change avatar
     *
     * @param ChangeAvatarRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function changeProfilePicture(ChangeAvatarRequest $request): JsonResponse
    {
        $user = $this->service->changeProfilePhoto(request()->user(), $request->validated());

        return $this->jsonSuccess(UserResource::make($user));
    }

    /**
     * Edit profile request
     *
     * @param EditProfileRequest $request
     *
     * @return JsonResponse
     */
    public function editProfile(EditProfileRequest $request): JsonResponse
    {
        $user = $this->service->editProfile(request()->user(), $request->validated());

        return $this->jsonSuccess(UserResource::make($user));
    }

    /**
     * Edit user category request
     *
     * @param EditUserCategoryRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function editCategory(EditUserCategoryRequest $request): JsonResponse
    {
        $user = $this->service->editCategory(request()->user(), $request->validated());

        return $this->jsonSuccess(UserResource::make($user));
    }

    /**
     * User wallet
     *
     * @param StoreWalletRequest $request
     *
     * @return JsonResponse
     */
    public function storeWallet(StoreWalletRequest $request): JsonResponse
    {
        WalletService::createWallet(request()->user(), 0, $request->validated()['amount'], 'title', request()->user());

        return $this->jsonSuccess([], __('Wallet Updated Successfully'));
    }

    /**
     * Seeker home
     *
     * @return JsonResponse
     */
    public function home(): JsonResponse
    {
        return $this->jsonSuccess(HomeResource::make($this->service->home(4)));
    }

    /**
     * Change Password Request
     *
     * @param ChangePasswordRequest $request
     *
     * @return JsonResponse
     */
    public function editPassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = request()->user();
        // Check if the old password matches the one stored in the database
        if (!Hash::check($request->old_password, $user->password)) {
            return $this->jsonError(
                __('The old password is incorrect'),
            );
        }

        $this->service->changePassword(request()->user(), $request->validated());

        return $this->jsonSuccess([], __('Password Changed Successfully'));
    }

    /**
     * Send custom push notification
     *
     * @param \App\Http\Requests\Api\User\SendNotificationRequest $request
     *
     * @return JsonResponse
     */
    public function sendNotification(\App\Http\Requests\Api\User\SendNotificationRequest $request): JsonResponse
    {
        $this->service->sendCustomNotification(request()->user(), $request->validated());

        return $this->jsonSuccess([], __('Notification sent successfully'));
    }
}
