<?php

declare(strict_types=1);

namespace App\Services\RolesPermissions;

use App\Repositories\Interfaces\RolesPermissions\RolePermissionRepositoryInterface;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Service layer for handling business logic related to the "RolePermissionRepositoryInterface" repository.
 */
class RolePermissionService
{
    public function __construct(
        protected RolePermissionRepositoryInterface $repository
    ) {}

    /**
     * Assign permission to role.
     * 
     * @param array $data
     * @return bool
     */
    public function assignPermissionToRole(array $data)
    {
        return $this->repository->assignPermissionToRole($data);
    }

    /**
     * Remove permission from role.
     * 
     * @param array $data
     * @return bool
     */
    public function removePermissionFromRole(array $data)
    {
        return $this->repository->removePermissionFromRole($data);
    }

    /**
     * Assign role to user.
     * 
     * @param array $data
     * @return bool
     */
    public function assignRoleToUser(array $data)
    {
        return $this->repository->assignRoleToUser($data);
    }

    /**
     * Remove role from user.
     * 
     * @param array $data
     * @return bool
     */
    public function revokeRoleFromUser(array $data)
    {
        return $this->repository->revokeRoleFromUser($data);
    }

    /**
     * Assign permission to user.
     * 
     * @param array $data
     * @return bool
     */
    public function assignPermissionToUser(array $data)
    {
        return $this->repository->assignPermissionToUser($data);
    }

    /**
     * Remove permission from user.
     * 
     * @param array $data
     * @return bool
     */
    public function revokePermissionFromUser(array $data)
    {
        return $this->repository->revokePermissionFromUser($data);
    }

    /**
     * Check user permissions.
     * 
     * @param array $data
     * @return bool
     */
    public function checkPermission(array $data)
    {
        return $this->repository->checkPermission($data);
    }

    /**
     * Get all permissions for user.
     * 
     * @param int|string $id
     * @return \Illuminate\Support\Collection
     */
    public function getUserPermissions(int|string $id)
    {
        return $this->repository->getUserPermissions($id);
    }
}
