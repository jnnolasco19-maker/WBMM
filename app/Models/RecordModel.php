<?php

namespace App\Models;

use CodeIgniter\Model;

class RecordModel extends Model
{
    protected $table         = 'records';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['vendor_id', 'stall_id', 'type', 'amount', 'description', 'record_date', 'created_by'];
    protected $useTimestamps = true;

    /**
     * Returns filtered records joined with vendors and stalls.
     */
    public function getFiltered(
        string $search   = '',
        string $type     = '',
        string $dateFrom = '',
        string $dateTo   = ''
    ): static {
        $this->select('records.*, vendors.name AS vendor_name, stalls.stall_number')
             ->join('vendors', 'vendors.id = records.vendor_id', 'left')
             ->join('stalls', 'stalls.id = records.stall_id', 'left');

        if ($search !== '') {
            $this->groupStart()
                 ->like('vendors.name', $search)
                 ->orLike('records.description', $search)
                 ->groupEnd();
        }

        if ($type !== '') {
            $this->where('records.type', $type);
        }

        if ($dateFrom !== '') {
            $this->where('records.record_date >=', $dateFrom);
        }

        if ($dateTo !== '') {
            $this->where('records.record_date <=', $dateTo);
        }

        $this->orderBy('records.record_date', 'DESC');

        return $this;
    }
}
