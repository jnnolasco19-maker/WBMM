<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table         = 'payments';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'vendor_id', 'amount', 'payment_type', 
        'period_start', 'period_end', 'reference_no', 
        'collected_by', 'notes', 'created_at'
    ];
    protected $returnType    = 'array';

    /**
     * Auto-generate reference_no format: ARK-YYYYMMDD-XXXX where XXXX is a random numeric sequence
     */
    public function generateReferenceNo(): string
    {
        $prefix = 'ARK-' . date('Ymd') . '-';
        do {
            $ref = $prefix . str_pad((string) mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $exists = $this->where('reference_no', $ref)->countAllResults();
        } while ($exists > 0);

        return $ref;
    }

    /**
     * Fetch payments joined with vendors and collectors
     */
    public function getFilteredPayments(string $search = '', string $vendorId = '', string $paymentType = '', string $collectedBy = '', string $dateFrom = '', string $dateTo = ''): static
    {
        $this->select('payments.*, vendors.name as vendor_name, vendors.stall_number, users.name as collector_name')
             ->join('vendors', 'vendors.id = payments.vendor_id')
             ->join('users', 'users.id = payments.collected_by', 'left');

        if ($search !== '') {
            $this->groupStart()
                 ->like('vendors.name', $search)
                 ->orLike('payments.reference_no', $search)
                 ->groupEnd();
        }

        if ($vendorId !== '') {
            $this->where('payments.vendor_id', (int) $vendorId);
        }

        if ($paymentType !== '') {
            $this->where('payments.payment_type', $paymentType);
        }

        if ($collectedBy !== '') {
            $this->where('payments.collected_by', (int) $collectedBy);
        }

        if ($dateFrom !== '') {
            $this->where('payments.created_at >=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo !== '') {
            $this->where('payments.created_at <=', $dateTo . ' 23:59:59');
        }

        return $this;
    }
}
