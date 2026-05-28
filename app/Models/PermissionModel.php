<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table         = 'permissions';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['permission_name', 'description'];
    protected $useTimestamps = true;
    protected $returnType    = 'array';

    /**
     * Find a permission by name
     */
    public function findByName(string $permissionName): ?array
    {
        return $this->where('permission_name', $permissionName)->first();
    }

    /**
     * Get all permissions for a specific role
     */
    public function getPermissionsByRole(int $roleId): array
    {
        return $this->select('permissions.*')
                    ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
                    ->where('role_permissions.role_id', $roleId)
                    ->findAll();
    }

    /**
     * Check if a role has a specific permission
     */
    public function roleHasPermission(int $roleId, string $permissionName): bool
    {
        $permission = $this->findByName($permissionName);
        if (!$permission) {
            return false;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('role_permissions');
        
        return $builder->where('role_id', $roleId)
                      ->where('permission_id', $permission['id'])
                      ->countAllResults() > 0;
    }

    /**
     * Assign permission to a role
     */
    public function assignPermissionToRole(int $roleId, int $permissionId): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('role_permissions');
        
        // Check if already assigned
        $exists = $builder->where('role_id', $roleId)
                         ->where('permission_id', $permissionId)
                         ->countAllResults() > 0;
        
        if ($exists) {
            return true;
        }

        return $builder->insert([
            'role_id' => $roleId,
            'permission_id' => $permissionId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Remove permission from a role
     */
    public function removePermissionFromRole(int $roleId, int $permissionId): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('role_permissions');
        
        return $builder->where('role_id', $roleId)
                      ->where('permission_id', $permissionId)
                      ->delete();
    }
}
