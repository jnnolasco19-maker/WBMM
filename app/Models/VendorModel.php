<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorModel extends Model
{
    protected $table         = 'vendors';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['name', 'stall_number', 'section', 'contact', 'permit_expiry', 'status', 'created_at'];
    protected $returnType    = 'array';

    /**
     * Get a filtered list of vendors with search capability
     */
    public function getFiltered(string $search = '', string $section = '', string $status = '', bool $expiringSoon = false): static
    {
        if ($search !== '') {
            $this->groupStart()
                 ->like('name', $search)
                 ->orLike('stall_number', $search)
                 ->orLike('contact', $search)
                 ->groupEnd();
        }

        if ($section !== '') {
            $this->where('section', $section);
        }

        if ($status !== '') {
            $this->where('status', $status);
        }

        if ($expiringSoon) {
            $thirtyDays = date('Y-m-d', strtotime('+30 days'));
            $this->where('permit_expiry <=', $thirtyDays);
        }

        return $this;
    }

    /**
     * Get all active vendors with overdue rent payments
     */
    public function getOverdueVendors(): array
    {
        $today = date('Y-m-d');
        return $this->db->table('vendors')
            ->select('vendors.*, MAX(payments.period_end) as last_payment_date')
            ->join('payments', 'payments.vendor_id = vendors.id', 'left')
            ->where('vendors.status', 'active')
            ->groupBy('vendors.id')
            ->having("last_payment_date IS NULL OR last_payment_date < '{$today}'")
            ->get()
            ->getResultArray();
    }

    /**
     * Get active vendors whose permit expires within 30 days
     */
    public function getExpiringPermits(): array
    {
        $today = date('Y-m-d');
        $thirtyDays = date('Y-m-d', strtotime('+30 days'));
        return $this->where('status', 'active')
                    ->where('permit_expiry >=', $today)
                    ->where('permit_expiry <=', $thirtyDays)
                    ->orderBy('permit_expiry', 'ASC')
                    ->findAll();
    }
}
