<?php

namespace App\Http\Controllers\Admin\Permission;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\Permission\AddPermissionRequest;
use App\Http\Requests\Admin\Permission\EditPermissionRequest;
use App\Http\Resources\Admin\Permission\PermissionResource;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Permission;

/**
 * A class defines the permissions actions
 */
class PermissionController extends BaseAdminController
{
    /**
     * List of the system permissions grouped by category
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $permissions = Permission::query()->orderBy('name')->get();

        // Group permissions by category
        $groupedPermissions = [
            'users' => [],
            'kyc' => [],
            'orders' => [],
            'projects' => [],
            'services' => [],
            'portfolios' => [],
            'wallets' => [],
            'payment_requests' => [],
            'commissions' => [],
            'reports' => [],
            'chats' => [],
            'categories' => [],
            'sub_categories' => [],
            'skills' => [],
            'countries' => [],
            'certificates' => [],
            'certificate_providers' => [],
            'refunds' => [],
            'analysis' => [],
            'commission_settings' => [],
            'features' => [],
            'banners' => [],
            'faqs' => [],
            'colors' => [],
            'pages' => [],
            'notification_settings' => [],
            'roles' => [],
            'permissions' => [],
            'other' => []
        ];

        foreach ($permissions as $permission) {
            $permissionResource = PermissionResource::make($permission);
            $name = strtolower($permission->name);

            if (str_contains($name, 'user')) {
                $groupedPermissions['users'][] = $permissionResource;
            } elseif (str_contains($name, 'kyc')) {
                $groupedPermissions['kyc'][] = $permissionResource;
            } elseif (str_contains($name, 'order')) {
                $groupedPermissions['orders'][] = $permissionResource;
            } elseif (str_contains($name, 'project')) {
                $groupedPermissions['projects'][] = $permissionResource;
            } elseif (str_contains($name, 'service')) {
                $groupedPermissions['services'][] = $permissionResource;
            } elseif (str_contains($name, 'portfolio')) {
                $groupedPermissions['portfolios'][] = $permissionResource;
            } elseif (str_contains($name, 'wallet')) {
                $groupedPermissions['wallets'][] = $permissionResource;
            } elseif (str_contains($name, 'payment request')) {
                $groupedPermissions['payment_requests'][] = $permissionResource;
            } elseif (str_contains($name, 'commission') && !str_contains($name, 'setting')) {
                $groupedPermissions['commissions'][] = $permissionResource;
            } elseif (str_contains($name, 'report')) {
                $groupedPermissions['reports'][] = $permissionResource;
            } elseif (str_contains($name, 'chat')) {
                $groupedPermissions['chats'][] = $permissionResource;
            } elseif (str_contains($name, 'categories') && !str_contains($name, 'sub')) {
                $groupedPermissions['categories'][] = $permissionResource;
            } elseif (str_contains($name, 'sub categories')) {
                $groupedPermissions['sub_categories'][] = $permissionResource;
            } elseif (str_contains($name, 'skill')) {
                $groupedPermissions['skills'][] = $permissionResource;
            } elseif (str_contains($name, 'countr')) {
                $groupedPermissions['countries'][] = $permissionResource;
            } elseif (str_contains($name, 'certificate') && !str_contains($name, 'provider')) {
                $groupedPermissions['certificates'][] = $permissionResource;
            } elseif (str_contains($name, 'certificate provider')) {
                $groupedPermissions['certificate_providers'][] = $permissionResource;
            } elseif (str_contains($name, 'refund')) {
                $groupedPermissions['refunds'][] = $permissionResource;
            } elseif (str_contains($name, 'analysis')) {
                $groupedPermissions['analysis'][] = $permissionResource;
            } elseif (str_contains($name, 'commission setting')) {
                $groupedPermissions['commission_settings'][] = $permissionResource;
            } elseif (str_contains($name, 'feature')) {
                $groupedPermissions['features'][] = $permissionResource;
            } elseif (str_contains($name, 'banner')) {
                $groupedPermissions['banners'][] = $permissionResource;
            } elseif (str_contains($name, 'faq')) {
                $groupedPermissions['faqs'][] = $permissionResource;
            } elseif (str_contains($name, 'color')) {
                $groupedPermissions['colors'][] = $permissionResource;
            } elseif (str_contains($name, 'page')) {
                $groupedPermissions['pages'][] = $permissionResource;
            } elseif (str_contains($name, 'notification setting')) {
                $groupedPermissions['notification_settings'][] = $permissionResource;
            } elseif (str_contains($name, 'role')) {
                $groupedPermissions['roles'][] = $permissionResource;
            } elseif (str_contains($name, 'permission')) {
                $groupedPermissions['permissions'][] = $permissionResource;
            } else {
                $groupedPermissions['other'][] = $permissionResource;
            }
        }

        // Remove empty categories
        $groupedPermissions = array_filter($groupedPermissions, function($category) {
            return !empty($category);
        });

        return $this->jsonSuccess($groupedPermissions);
    }

    /**
     * Store new permission
     *
     * @param AddPermissionRequest $request
     *
     * @return JsonResponse
     */
    public function store(AddPermissionRequest $request): JsonResponse
    {
        $permission = Permission::create([
            'name' => $request->input('name'),
            'name_ar' => $request->input('name_ar'),
        ]);

        return $this->jsonSuccess(PermissionResource::make($permission));
    }

    /**
     * Edit permission
     *
     * @param Permission $permission
     * @param EditPermissionRequest $request
     *
     * @return JsonResponse
     */
    public function update(Permission $permission, EditPermissionRequest $request): JsonResponse
    {
        $permission->update([
            'name' => $request->input('name'),
            'name_ar' => $request->input('name_ar'),
        ]);

        return $this->jsonSuccess(PermissionResource::make($permission));
    }

    /**
     * Delete permission
     *
     * @param Permission $permission
     *
     * @return JsonResponse
     */
    public function delete(Permission $permission): JsonResponse
    {
        $permission->delete();

        return $this->jsonSuccess([], __('Permission deleted successfully'));
    }

    /**
     * Show permission details
     *
     * @param Permission $permission
     *
     * @return JsonResponse
     */
    public function show(Permission $permission): JsonResponse
    {
        return $this->jsonSuccess(PermissionResource::make($permission));
    }
}
