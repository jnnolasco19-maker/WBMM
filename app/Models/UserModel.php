<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['name', 'email', 'password', 'role', 'status', 'created_at'];
    protected $returnType    = 'array';

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }
}
