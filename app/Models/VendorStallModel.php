<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorStallModel extends Model
{
    protected $table         = 'vendor_stalls';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'vendor_id', 'stall_id', 'permit_no', 'permit_issued',
        'permit_expiry', 'assigned_date', 'status',
        'terminated_date', 'notes',
    ];
    protected $useTimestamps = false;
    protected $returnType    = 'array';

    // ── Active assignments for a vendor ──────────────────────

    public function getActiveByVendor(int $vendorId): array
    {
        return $this->db->table('vendor_stalls vs')
            ->select('vs.*, s.stall_code, s.section, s.type AS stall_type,
                      s.sqm, s.floor_level, s.status AS stall_status')
            ->join('stalls s', 's.id = vs.stall_id')
            ->where('vs.vendor_id', $vendorId)
            ->where('vs.status', 'active')
            ->orderBy('s.stall_code', 'ASC')
            ->get()->getResultArray();
    }

    // ── All assignments for a vendor (history) ───────────────

    public function getAllByVendor(int $vendorId): array
    {
        return $this->db->table('vendor_stalls vs')
            ->select('vs.*, s.stall_code, s.section, s.type AS stall_type, s.sqm')
            ->join('stalls s', 's.id = vs.stall_id')
            ->where('vs.vendor_id', $vendorId)
            ->orderBy('vs.assigned_date', 'DESC')
            ->get()->getResultArray();
    }

    // ── All assignments for a stall (history) ────────────────

    public function getAllByStall(int $stallId): array
    {
        return $this->db->table('vendor_stalls vs')
            ->select('vs.*, CONCAT(v.first_name," ",v.last_name) AS vendor_name,
                      v.vendor_no, v.business_name')
            ->join('vendors v', 'v.id = vs.vendor_id')
            ->where('vs.stall_id', $stallId)
            ->orderBy('vs.assigned_date', 'DESC')
            ->get()->getResultArray();
    }

    // ── Active assignment for a stall ────────────────────────

    public function getActiveByStall(int $stallId): ?array
    {
        return $this->db->table('vendor_stalls vs')
            ->select('vs.*, CONCAT(v.first_name," ",v.last_name) AS vendor_name,
                      v.vendor_no, v.business_name, v.contact,
                      v.id AS vendor_id')
            ->join('vendors v', 'v.id = vs.vendor_id')
            ->where('vs.stall_id', $stallId)
            ->where('vs.status', 'active')
            ->get()->getRowArray() ?: null;
    }

    // ── Check if stall is already occupied ───────────────────

    public function isStallOccupied(int $stallId): bool
    {
        return $this->where('stall_id', $stallId)
                    ->where('status', 'active')
                    ->countAllResults() > 0;
    }

    // ── Full list with joins for index page ──────────────────

    public function getAll(): array
    {
        return $this->db->table('vendor_stalls vs')
            ->select('vs.*,
                      CONCAT(v.first_name," ",v.last_name) AS vendor_name,
                      v.vendor_no, v.business_name, v.type AS vendor_type,
                      s.stall_code, s.section, s.type AS stall_type, s.sqm')
            ->join('vendors v', 'v.id = vs.vendor_id')
            ->join('stalls s',  's.id = vs.stall_id')
            ->orderBy('vs.status', 'ASC')
            ->orderBy('vs.assigned_date', 'DESC')
            ->get()->getResultArray();
    }

    // ── Single assignment detail ─────────────────────────────

    public function getDetail(int $id): ?array
    {
        return $this->db->table('vendor_stalls vs')
            ->select('vs.*,
                      CONCAT(v.first_name," ",v.last_name) AS vendor_name,
                      v.vendor_no, v.business_name, v.type AS vendor_type,
                      s.stall_code, s.section, s.type AS stall_type, s.sqm')
            ->join('vendors v', 'v.id = vs.vendor_id')
            ->join('stalls s',  's.id = vs.stall_id')
            ->where('vs.id', $id)
            ->get()->getRowArray() ?: null;
    }
}
