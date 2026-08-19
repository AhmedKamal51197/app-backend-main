<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Admin\Users\UsersResource;
use App\Models\Setting;
use App\Models\User;
use App\Services\Users\UsersService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the user controller's actions
 */
class UsersController extends BaseAdminController
{
    /**
     * Load the service
     *
     * @param UsersService $service
     */
    public function __construct(protected UsersService $service)
    {
    }

    /**
     * List of the system users
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $result = $this->service->index(
            $request->input('search', ''),
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('page', Setting::PAGE),
            $request->input('role', ''),
            $request->input('active', ''),
            $request->input('order_by', ''),
        );

        return $this->jsonSuccess([
            'users' => UsersResource::collection($result['users']),
            'analytics' => $result['analytics'],
            'meta' => $result['meta'],
        ]);
    }

    /**
     * List of seekers
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function seekers(Request $request): JsonResponse
    {
        return $this->jsonSuccess(UsersResource::collection($this->service->indexByRole($request->input('search', ''), 'seeker', $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * List of providers
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function providers(Request $request): JsonResponse
    {
        return $this->jsonSuccess(UsersResource::collection($this->service->indexByRole($request->input('search', ''), 'provider', $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * List of admins
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function admins(Request $request): JsonResponse
    {
        return $this->jsonSuccess(UsersResource::collection($this->service->indexByRole($request->input('search', ''), 'root', $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * Show user
     *
     * @param User $user
     *
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        $user = $this->service->show($user);

        return $this->jsonSuccess(UsersResource::make($user));
    }

    /**
     * Delete user
     *
     * @param User $user
     *
     * @return JsonResponse
     */
    public function delete(User $user): JsonResponse
    {
        $user->delete();

        return $this->jsonSuccess([], __('User deleted'));
    }

    /**
     * Toggle user's active status
     *
     * @param User $user
     *
     * @return JsonResponse
     */
    public function toggleActive(User $user): JsonResponse
    {
        $user = $this->service->toggleActive($user);

        return $this->jsonSuccess(UsersResource::make($user), __('User status updated'));
    }

    /**
     * Get users analytics
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function analytics(Request $request): JsonResponse
    {
        $result = $this->service->usersAnalytics(
            $request->input('period', 'month'),
            $request->input('role')
        );

        return $this->jsonSuccess($result);
    }
}
