<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
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
     * Update a user's password hash by user ID.
     */
    public function updatePassword(int $id, string $hash): bool
    {
        return $this->update($id, ['password' => $hash]);
    }
}
