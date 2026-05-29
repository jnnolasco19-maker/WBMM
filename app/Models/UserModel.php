<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['name', 'email', 'password', 'role', 'status'];
    protected $useTimestamps = false;
    protected $returnType    = 'array';

    // ── Finders ─────────────────────────────────────────────

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    public function getActiveCollectors(): array
    {
        return $this->where('role', 'collector')
                    ->where('status', 'active')
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    public function getAllUsers(): array
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }
}
