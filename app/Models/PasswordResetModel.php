<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table         = 'password_resets';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['email', 'token', 'expires_at', 'used'];
    protected $useTimestamps = true;
    protected $updatedField  = ''; // no updated_at column
    protected $returnType    = 'array';

    /**
     * Insert a new password reset token record.
     */
    public function createToken(string $email, string $token, string $expiresAt): bool
    {
        return $this->insert([
            'email'      => $email,
            'token'      => $token,
            'expires_at' => $expiresAt,
            'used'       => 0,
        ]);
    }

    /**
     * Find a valid (unused, unexpired) token.
     */
    public function findValidToken(string $token): ?array
    {
        return $this->where('token', $token)
                    ->where('used', 0)
                    ->where('expires_at >', date('Y-m-d H:i:s'))
                    ->first();
    }

    /**
     * Mark a token as used so it cannot be reused.
     */
    public function invalidateToken(string $token): bool
    {
        return $this->where('token', $token)->set(['used' => 1])->update();
    }
}
