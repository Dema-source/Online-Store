<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\RolesPermissions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Role\RolePermissionRequest;
use App\Http\Requests\Api\Role\UserPermissionsRequest;
use App\Http\Requests\Api\Role\UserRolesRequest;
use App\Services\RolesPermissions\RolePermissionService;
use Illuminate\Http\JsonResponse;

class RolePermissionController extends Controller
{
    /**
     * RolePermissionController Constructor.
     * 
     * @param RolePermissionService $rolepermissionService
     */
    public function __construct(protected RolePermissionService $rolepermissionService) {}

    /**
     * Assign permission to role.
     * 
     * @param RolePermissionRequest $request
     * @return JsonResponse
     */
    public function assignPermissionToRole(RolePermissionRequest $request): JsonResponse
    {
        $this->rolepermissionService->assignPermissionToRole($request->validated());
        return $this->success(null, 'Assign permission to role successfully');
    }

    /**
     * Remove permission from role.
     * 
     * @param RolePermissionRequest $request
     * @return JsonResponse
     */
    public function removePermissionFromRole(RolePermissionRequest $request): JsonResponse
    {
        $this->rolepermissionService->removePermissionFromRole($request->validated());
        return $this->success(null, 'Remove permission from role successfully');
    }

    /**
     * assign role to a specifice user.
     * 
     * @param UserRolesRequest $request
     * @return JsonResponse
     */
    public function assignRoleToUser(UserRolesRequest $request): JsonResponse
    {
        $this->rolepermissionService->assignRoleToUser($request->validated());
        return $this->success(null, 'Role assigned successfully');
    }

    /**
     * Remove role from user.
     * 
     * @param UserRolesRequest $request
     * @return JsonResponse
     */
    public function revokeRoleFromUser(UserRolesRequest $request): JsonResponse
    {
        $this->rolepermissionService->revokeRoleFromUser($request->validated());
        return $this->success(null, 'Role removed successfully');
    }

    /**
     * Assign permission to user.
     * 
     * @param UserPermissionsRequest $request
     * @return JsonResponse
     */
    public function assignPermissionToUser(UserPermissionsRequest $request): JsonResponse
    {
        $this->rolepermissionService->assignPermissionToUser($request->validated());
        return $this->success(null, 'Permission assigned successfully');
    }

    /**
     * Remove permission from user.
     * 
     * @param UserPermissionsRequest $request
     * @return JsonResponse
     */
    public function revokePermissionFromUser(UserPermissionsRequest $request): JsonResponse
    {
        $this->rolepermissionService->revokePermissionFromUser($request->validated());
        return $this->success(null, 'Permission removed successfully');
    }

    /**
     * Check if user has a specific permission.
     * 
     * @param UserPermissionsRequest $request
    //  * @return bool
     */
    public function checkPermission(UserPermissionsRequest $request)
    {
        $result = $this->rolepermissionService->checkPermission($request->validated());
        return $this->success($result, 'Permission checked successfully');
    }

    /**
     * Get all permissions for user.
     * 
     * @param int|string $id
     * @return JsonResponse
     */
    public function getUserPermissions(int|string $id)
    {
        $permissions = $this->rolepermissionService->getUserPermissions($id);
        return $this->success($permissions, 'Permissions fetched successfully');
    }
}
