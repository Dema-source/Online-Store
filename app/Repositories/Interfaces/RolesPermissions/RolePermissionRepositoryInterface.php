<?php

namespace App\Repositories\Interfaces\RolesPermissions;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Interface RolePermissionRepositoryInterface
 *
 * Define operations related to Roles and Permissions
 */
interface RolePermissionRepositoryInterface
{
    /**
     * Assign permission to role.
     * 
     * @param array $data
     * @return bool
     */
    public function assignPermissionToRole(array $data);

    /**
     * Remove permission from role.
     * 
     * @param array $data
     * @return bool
     */
    public function removePermissionFromRole(array $data);

    /**
     * Assign role to user.
     * 
     * @param array $data
     * @return bool
     */
    public function assignRoleToUser(array $data);

    /**
     * Remove role from user.
     * 
     * @param array $data
     * @return bool
     */
    public function revokeRoleFromUser(array $data);

    /**
     * Assign permission to user.
     * 
     * @param array $data
     * @return bool
     */
    public function assignPermissionToUser(array $data);

    /**
     * Remove permission from user.
     * 
     * @param array $data
     * @return bool
     */
    public function revokePermissionFromUser(array $data);

    /**
     * Check user permissions.
     * 
     * @param array $data
     * @return bool
     */
    public function checkPermission(array $data);

    /**
     * Get all permissions for user.
     * 
     * @param int|string $id
     * @return Collection
     */
    public function getUserPermissions(int|string $id);
}
