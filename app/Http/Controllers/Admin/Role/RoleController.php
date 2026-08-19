<?php

namespace App\Http\Controllers\Admin\Role;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\Role\AddRoleRequest;
use App\Http\Requests\Admin\Role\AddSupervisorRequest;
use App\Http\Requests\Admin\Role\DeleteRoleRequest;
use App\Http\Requests\Admin\Role\EditRoleRequest;
use App\Http\Requests\Admin\Role\EditSupervisorRequest;
use App\Http\Requests\Admin\Role\UpdateSupervisorPasswordRequest;
use App\Http\Resources\Admin\Role\RoleResource;
use App\Http\Resources\Admin\User\UserResource;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\Role\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Permission;

/**
 * A class defines the role actions
 */
class RoleController extends BaseAdminController
{
    /**
     * RoleController constructor
     *
     * @param RoleService $service
     */
    public function __construct(protected RoleService $service) {}
    /**
     * List of the system roles
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $roles = $this->service->index(
            $request->input('limit', Setting::PAGE_RESULT_LIMIT)
        );

        return $this->jsonSuccess(RoleResource::collection($roles));
    }

    /**
     * Store new role
     *
     * @param AddRoleRequest $request
     *
     * @return JsonResponse
     */
    public function store(AddRoleRequest $request): JsonResponse
    {
        $role = $this->service->create($request->validated());

        return $this->jsonSuccess(RoleResource::make($role), __('Role created successfully'));
    }

    /**
     * Edit role
     *
     * @param Role $role
     * @param EditRoleRequest $request
     *
     * @return JsonResponse
     */
    public function update(EditRoleRequest $request, Role $role): JsonResponse
    {
        $role = $this->service->update($role, $request->validated());

        return $this->jsonSuccess(RoleResource::make($role), __('Role updated successfully'));
    }

    /**
     * Delete role
     * If role has users, they will be moved to the new role if provided
     *
     * @param Role $role
     * @param DeleteRoleRequest $request
     *
     * @return JsonResponse
     */
    public function destroy(Role $role, DeleteRoleRequest $request): JsonResponse
    {
        $this->service->delete($role, $request->validated());

        return $this->jsonSuccess([], __('Role deleted successfully'));
    }

    /**
     * Show role
     *
     * @param Role $role
     *
     * @return JsonResponse
     */
    public function show(Role $role): JsonResponse
    {
        $role->load('permissions');
        return $this->jsonSuccess(RoleResource::make($role));
    }

    /**
     * Add role permission
     *
     * @param Role $role
     * @param Permission $permission
     *
     * @return JsonResponse
     */
    public function addPermission(Role $role, Permission $permission): JsonResponse
    {
        $role->givePermissionTo($permission->name);

        return $this->jsonSuccess([], __('Permission added successfully'));
    }

    /**
     * Add role permission
     *
     * @param Role $role
     * @param Permission $permission
     *
     * @return JsonResponse
     */
    public function deletePermission(Role $role, Permission $permission): JsonResponse
    {
        $role->revokePermissionTo($permission->name);

        return $this->jsonSuccess([], __('Permission removed from role successfully'));
    }

    /**
     * Toggle role active status
     *
     * @param Role $role
     *
     * @return JsonResponse
     */
    public function toggleActive(Role $role): JsonResponse
    {
        $role = $this->service->toggleActive($role);

        return $this->jsonSuccess(RoleResource::make($role), __('Role active status toggled successfully'));
    }

    /**
     * List users with supervisor roles
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function supervisorUsers(Request $request): JsonResponse
    {
        $users = $this->service->getSupervisorUsers(
            $request->input('limit', Setting::PAGE_RESULT_LIMIT)
        );

        return $this->jsonSuccess(UserResource::collection($users));
    }

    /**
     * Show supervisor user details
     *
     * @param User $user
     *
     * @return JsonResponse
     */
    public function showSupervisorUser(User $user): JsonResponse
    {
        return $this->jsonSuccess(UserResource::make($user));
    }

    /**
     * Create a new supervisor
     *
     * @param AddSupervisorRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function createSupervisor(AddSupervisorRequest $request): JsonResponse
    {
        $user = $this->service->createSupervisor($request->validated());

        return $this->jsonSuccess(UserResource::make($user), __('Supervisor created successfully'));
    }

    /**
     * Edit supervisor
     *
     * @param User $user
     * @param EditSupervisorRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function editSupervisor(User $user, EditSupervisorRequest $request): JsonResponse
    {
        $user = $this->service->editSupervisor($user, $request->validated());

        return $this->jsonSuccess(UserResource::make($user), __('Supervisor updated successfully'));
    }

    /**
     * Update supervisor password
     *
     * @param User $user
     * @param UpdateSupervisorPasswordRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function updateSupervisorPassword(User $user, UpdateSupervisorPasswordRequest $request): JsonResponse
    {
        $this->service->updateSupervisorPassword($user, $request->validated());

        return $this->jsonSuccess([], __('Supervisor password updated successfully'));
    }
}
