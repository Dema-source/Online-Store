<?php

namespace App\Repositories\Eloquent\RolesPermissions;

use App\Repositories\Interfaces\RolesPermissions\RolePermissionRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionRepository implements RolePermissionRepositoryInterface
{
    /**
     * Find permission by name.
     * 
     * @param string $name
     * @return Permission|\Spatie\Permission\Contracts\Permission
     */
    public function findByPermissionName(string $name): ?Permission
    {
        return Permission::findByName($name, 'sanctum');
    }

    /**
     * Find role by name.
     * 
     * @param string $name
     * @return Role|\Spatie\Permission\Contracts\Role
     */
    public function findByRoleName(string $name): ?Role
    {
        return Role::findByName($name, 'sanctum');
    }

    /**
     * Assign permission to role.
     * 
     * @param array $data
     * @return bool
     */
    public function assignPermissionToRole(array $data)
    {
        $role = $this->findByRoleName($data['role']);
        foreach ($data['permissions'] as $permission) {
            $permission = $this->findByPermissionName($permission);
            if (! $permission)
                continue;
            $role->givePermissionTo($permission);
        }
        return true;
    }

    /**
     * Remove permission from role.
     * 
     * @param array $data
     * @return bool
     */
    public function removePermissionFromRole(array $data)
    {
        $role = $this->findByRoleName($data['role']);
        foreach ($data['permissions'] as $permission) {
            $permission =  $this->findByPermissionName($permission);
            if (! $permission)
                continue;
            $role->revokePermissionTo($permission);
        }
        return true;
    }

    /**
     * Assign role to user.
     * 
     * @param array $data
     * @return bool
     */
    public function assignRoleToUser(array $data)
    {
        $user = User::findOrFail($data['user_id']);
        foreach ($data['roles'] as $role) {
            $role =  $this->findByRoleName($role);
            if (! $role)
                continue;
            $user->assignRole($role);
        }
        return true;
    }

    /**
     * Remove role from user.
     * 
     * @param array $data
     * @return bool
     */
    public function revokeRoleFromUser(array $data)
    {
        $user = User::findOrFail($data['user_id']);
        foreach ($data['roles'] as $role) {
            $role = $this->findByRoleName($role);
            if (! $role)
                continue;
            $user->removeRole($role);
        }
        return true;
    }

    /**
     * Assign permission to user.
     * 
     * @param array $data
     * @return bool
     */
    public function assignPermissionToUser(array $data)
    {
        $user = User::findOrFail($data['user_id']);
        foreach ($data['permissions'] as $permission) {
            $permission =  $this->findByPermissionName($permission);
            if (! $permission)
                continue;
            $user->givePermissionTo($permission);
        }
        return true;
    }

    /**
     * Remove permission from user.
     * 
     * @param array $data
     * @return bool
     */
    public function revokePermissionFromUser(array $data)
    {
        $user = User::findOrFail($data['user_id']);
        foreach ($data['permissions'] as $permission) {
            $permission = $this->findByPermissionName($permission);
            if (! $permission)
                continue;
            $user->revokePermissionTo($permission);
        }
        return true;
    }

    /**
     * Check user permissions.
     * 
     * @param array $data
     * @return bool
     */
    public function checkPermission(array $data)
    {
        $user = User::findOrFail($data['user_id']);
        foreach ($data['permissions'] as $permission) {
            $permission = $this->findByPermissionName($permission);
            if (! $permission)
                continue;
            if (!$user->hasPermissionTo($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all permissions for user.
     *  
     * @param int|string $id
     * @return collection
     */
    public function getUserPermissions(int|string $id): Collection
    {
        $user = User::findOrFail($id);
        return $user->getAllPermissions();
    }
}
