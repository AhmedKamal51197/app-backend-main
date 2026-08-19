<?php

namespace App\Services\Role;

use App\Events\LogExceptionEvent;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the role service
 */
class RoleService
{
    /**
     * Get all roles with pagination
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage): LengthAwarePaginator
    {
        return Role::query()
            ->with('permissions')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Create a new role
     *
     * @param array $data
     *
     * @return Role
     *
     * @throws Exception
     */
    public function create(array $data): Role
    {
        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $data['name'],
                'name_ar' => $data['name_ar'] ?? null,
                'allowed_user' => $data['allowed_user'],
                'is_active' => $data['is_active'] ?? true,
                'description' => $data['description'] ?? null,
                'type' => $data['type'] ??  null,
                'access_level' => $data['access_level'] ?? null,
            ]);

            if (!empty($data['permissions'])) {
                $role->givePermissionTo($data['permissions']);
            }

            DB::commit();

            return $role;

        } catch (Exception $exception) {
            event(new LogExceptionEvent($exception));
            DB::rollBack();

            throw $exception;
        }
    }

    /**
     * Update role
     *
     * @param Role $role
     * @param array $data
     *
     * @return Role
     */
    public function update(Role $role, array $data): Role
    {
        DB::beginTransaction();

        try {
            $role->update([
                'name' => $data['name'] ?? $role->name,
                'name_ar' => $data['name_ar'] ?? $role->name_ar,
                'allowed_user' => $data['allowed_user']  ?? $role->allowed_user,
                'is_active' => $data['is_active'] ?? $role->is_active,
                'description' => $data['description'] ?? $role->description,
                'type' => $data['type'] ??  $role->type,
                'access_level' => $data['access_level'] ?? $role->access_level,
            ]);

            if (isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            DB::commit();

            return $role->fresh();

        } catch (Exception $exception) {
            event(new LogExceptionEvent($exception));
            DB::rollBack();

            throw $exception;
        }
    }

    /**
     * Toggle role active status
     *
     * @param Role $role
     *
     * @return Role
     */
    public function toggleActive(Role $role): Role
    {
        $role->update(['is_active' => !$role->is_active]);

        return $role->fresh();
    }

    /**
     * Delete role and move users to 'user' role
     *
     * @param Role $role
     * @param array $data
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(Role $role, array $data): bool
    {
        if (in_array($role->name, ['root', 'user'])) {
            throw new Exception(__("Cannot delete default system roles"));
        }

        $usersCount = $role->users()->count();

        if ($usersCount > 0) {
            $newRole = Role::where('name', 'user')->first();

            if (!$newRole) {
                throw new Exception(__("Fallback user role not found"));
            }

            if ($newRole->id === $role->id) {
                throw new Exception(__("Cannot move users to the same role"));
            }

            DB::beginTransaction();
            try {
                foreach ($role->users as $user) {
                    $user->syncRoles([$newRole->name]);
                }

                $role->delete();

                DB::commit();

            } catch (Exception $exception) {
                event(new LogExceptionEvent($exception));
                DB::rollBack();

                throw $exception;
            }
        } else {
            $role->delete();
        }

        return true;
    }

    /**
     * Get users with supervisor roles
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getSupervisorUsers(int $perPage): LengthAwarePaginator
    {
        return User::whereHas('roles', function ($query) {
            $query->where('allowed_user', false);
        })
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Create a new supervisor user
     *
     * @param array $data
     *
     * @return User
     *
     * @throws Exception
     */
    public function createSupervisor(array $data): User
    {
        DB::beginTransaction();

        try {
            $role = Role::where('uuid', $data['role_id'])->firstOrFail();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'password' => bcrypt($data['password']),
                'active' => true,
                'email_verified_at' => now(),
                'mobile_verified_at' => now(),
            ]);

            $user->assignRole($role->name);

            DB::commit();

            return $user->fresh(['roles', 'avatar']);

        } catch (Exception $exception) {
            event(new LogExceptionEvent($exception));
            DB::rollBack();

            throw $exception;
        }
    }

    /**
     * Edit supervisor user
     *
     * @param User $user
     * @param array $data
     *
     * @return User
     *
     * @throws Exception
     */
    public function editSupervisor(User $user, array $data): User
    {
        DB::beginTransaction();

        try {
            $role = Role::where('uuid', $data['role_id'])->firstOrFail();

            if ($role->allowed_user) {
                throw new Exception(__('Cannot assign regular user role to supervisor'));
            }

            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
            ]);

            $user->syncRoles([$role->name]);

            DB::commit();

            return $user->fresh(['roles', 'avatar']);

        } catch (Exception $exception) {
            event(new LogExceptionEvent($exception));
            DB::rollBack();

            throw $exception;
        }
    }

    /**
     * Update supervisor password
     *
     * @param User $user
     * @param array $data
     *
     * @return void
     *
     * @throws Exception
     */
    public function updateSupervisorPassword(User $user, array $data): void
    {
        DB::beginTransaction();

        try {
            $user->update([
                'password' => bcrypt($data['password']),
            ]);

            DB::commit();

        } catch (Exception $exception) {
            event(new LogExceptionEvent($exception));
            DB::rollBack();

            throw $exception;
        }
    }
}
