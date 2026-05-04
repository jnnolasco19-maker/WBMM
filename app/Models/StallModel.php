<?php

namespace App\Models;

use CodeIgniter\Model;

class StallModel extends Model
{
    protected $table         = 'stalls';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['stall_number', 'location', 'size', 'status', 'vendor_id'];
    protected $useTimestamps = true;

    /**
     * Returns filtered stalls joined with vendors for vendor name display.
     */
    public function getFiltered(string $search = '', string $status = ''): static
    {
        $this->select('stalls.*, vendors.name AS vendor_name')
             ->join('vendors', 'vendors.id = stalls.vendor_id', 'left');

        if ($search !== '') {
            $this->groupStart()
                 ->like('stalls.stall_number', $search)
                 ->orLike('stalls.location', $search)
                 ->groupEnd();
        }

        if ($status !== '') {
            $this->where('stalls.status', $status);
        }

        return $this;
    }

    /**
     * Sets vendor_id to NULL and status to 'vacant' for all stalls assigned to a vendor.
     */
    public function vacateByVendor(int $vendorId): bool
    {
        return $this->where('vendor_id', $vendorId)
                    ->set(['vendor_id' => null, 'status' => 'vacant'])
                    ->update();
    }
}
