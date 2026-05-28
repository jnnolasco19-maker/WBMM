<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table         = 'roles';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['role_name', 'description', 'level'];
    protected $useTimestamps = true;
    protected $returnType    = 'array';

    /**
     * Find a role by name
     */
    public function findByName(string $roleName): ?array
    {
        return $this->where('role_name', $roleName)->first();
    }

    /**
     * Get role with permissions
     */
    public function getRoleWithPermissions(int $roleId): ?array
    {
        $role = $this->find($roleId);
        if (!$role) {
            return null;
        }

        $permissionModel = new PermissionModel();
        $role['permissions'] = $permissionModel->getPermissionsByRole($roleId);

        return $role;
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission(int $roleId, string $permissionName): bool
    {
        $permissionModel = new PermissionModel();
        return $permissionModel->roleHasPermission($roleId, $permissionName);
    }
}
