<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';

    // Removed role_id
    protected $allowedFields = ['name', 'email', 'password', 'role'];

    protected $useTimestamps = true;
    protected $returnType    = 'array';

    /**
     * Find a user by email — safe, excludes password field.
     * Use this for displaying user data to other modules.
     */
    public function findByEmail(string $email): ?array
    {
        return $this->select('id, name, email, role')
                    ->where('email', $email)
                    ->first();
    }

    /**
     * Find a user by email — includes password hash.
     * Use this ONLY for authentication (login).
     */
    public function findByEmailWithPassword(string $email): ?array
    {
        return $this->select('id, name, email, password, role')
                    ->where('email', $email)
                    ->first();
    }

    /**
     * Find user by ID
     */
    public function findWithRole(int $userId): ?array
    {
        return $this->select('id, name, email, role')
                    ->where('id', $userId)
                    ->first();
    }

    /**
     * Get user's role name
     */
    public function getUserRoleName(int $userId): ?string
    {
        $user = $this->findWithRole($userId);

        return $user ? $user['role'] : null;
    }

    /**
     * Get user's role level
     * Example levels:
     * admin = 1
     * user = 2
     */
    public function getUserRoleLevel(int $userId): ?int
    {
        $user = $this->findWithRole($userId);

        if (! $user) {
            return null;
        }

        return match ($user['role']) {
            'admin' => 1,
            'user'  => 2,
            default => 0,
        };
    }

    /**
     * Update a user's password hash by user ID.
     */
    public function updatePassword(int $id, string $hash): bool
    {
        return $this->update($id, ['password' => $hash]);
    }
}