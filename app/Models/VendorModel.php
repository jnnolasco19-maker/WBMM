<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorModel extends Model
{
    protected $table         = 'vendors';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['name', 'contact_number', 'email', 'address', 'status'];
    protected $useTimestamps = true;

    /**
     * Returns a query builder result filtered by optional search term and status.
     * Caller should call ->paginate() or ->findAll() on the returned builder.
     */
    public function getFiltered(string $search = '', string $status = ''): static
    {
        if ($search !== '') {
            $this->groupStart()
                 ->like('name', $search)
                 ->orLike('email', $search)
                 ->orLike('contact_number', $search)
                 ->groupEnd();
        }

        if ($status !== '') {
            $this->where('status', $status);
        }

        return $this;
    }
}
