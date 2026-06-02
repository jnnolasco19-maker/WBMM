<?php

namespace App\Models;

use CodeIgniter\Model;

class StallModel extends Model
{
    protected $table         = 'stalls';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'stall_code', 'section', 'type', 'sqm',
        'floor_level', 'status', 'notes',
        'barangay_permit_no', 'barangay_permit_issued', 'barangay_permit_expiry',
    ];
    protected $useTimestamps = false;
    protected $returnType    = 'array';

    public function getFiltered(
        string $type = '',
        string $section = '',
        string $status = '',
        string $search = ''
    ): array {
        $builder = $this->db->table('stalls s')
            ->select('s.*, CONCAT(v.first_name," ",v.last_name) AS vendor_name, v.vendor_no')
            ->join('vendor_stalls vs', 'vs.stall_id = s.id AND vs.status = "active"', 'left')
            ->join('vendors v', 'v.id = vs.vendor_id', 'left');

        if ($type !== '') {
            $builder->where('s.type', $type);
        }
        if ($section !== '') {
            $builder->where('s.section', $section);
        }
        if ($status !== '') {
            $builder->where('s.status', $status);
        }
        if ($search !== '') {
            $builder->groupStart()
                ->like('s.stall_code', $search)
                ->orLike('s.section', $search)
                ->groupEnd();
        }

        return $builder->orderBy('s.stall_code', 'ASC')->get()->getResultArray();
    }

    public function getVacant(string $type = ''): array
    {
        $builder = $this->where('status', 'vacant');
        if ($type !== '') {
            $builder->where('type', $type);
        }

        return $builder->orderBy('stall_code', 'ASC')->findAll();
    }

    public function getDetail(int $id): ?array
    {
        return $this->db->table('stalls s')
            ->select('s.*, vs.id AS assignment_id, vs.permit_no, vs.permit_issued, vs.permit_expiry,
                      vs.assigned_date, vs.status AS assignment_status,
                      v.id AS vendor_id, v.vendor_no,
                      CONCAT(v.first_name," ",v.last_name) AS vendor_name,
                      v.business_name, v.contact')
            ->join('vendor_stalls vs', 'vs.stall_id = s.id AND vs.status = "active"', 'left')
            ->join('vendors v', 'v.id = vs.vendor_id', 'left')
            ->where('s.id', $id)
            ->get()->getRowArray() ?: null;
    }

    public function getSections(): array
    {
        return $this->db->table('stalls')
            ->distinct()
            ->select('section')
            ->orderBy('section', 'ASC')
            ->get()->getResultArray();
    }

    public function getVacantReport(): array
    {
        return $this->db->table('stalls s')
            ->select('s.*,
                      CONCAT(v.first_name," ",v.last_name) AS last_vendor_name,
                      v.vendor_no AS last_vendor_no,
                      vs.terminated_date')
            ->join('vendor_stalls vs', 'vs.stall_id = s.id', 'left')
            ->join('vendors v', 'v.id = vs.vendor_id', 'left')
            ->where('s.status', 'vacant')
            ->groupBy('s.id')
            ->orderBy('s.type', 'ASC')
            ->orderBy('s.section', 'ASC')
            ->orderBy('s.stall_code', 'ASC')
            ->get()->getResultArray();
    }

    public function countOccupiedInside(): int
    {
        return (int) $this->where('type', 'inside')->where('status', 'occupied')->countAllResults();
    }

    public function countOccupiedOutside(): int
    {
        return (int) $this->where('type', 'outside')->where('status', 'occupied')->countAllResults();
    }

    public function countVacant(): int
    {
        return (int) $this->where('status', 'vacant')->countAllResults();
    }
}
