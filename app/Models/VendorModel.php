<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorModel extends Model
{
    protected $table         = 'vendors';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'vendor_no', 'first_name', 'last_name', 'business_name',
        'contact', 'address', 'id_type', 'id_number', 'type', 'status',
    ];
    protected $useTimestamps = false;
    protected $returnType    = 'array';

    public function generateVendorNo(): string
    {
        $year   = date('Y');
        $prefix = 'VND-' . $year . '-';
        $count  = $this->like('vendor_no', $prefix, 'after')->countAllResults();

        return $prefix . str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
    }

    public function getFiltered(string $search = '', string $type = '', string $status = ''): array
    {
        $builder = $this->db->table('vendors v')
            ->select('v.*, COUNT(DISTINCT vs.id) AS stall_count')
            ->join('vendor_stalls vs', 'vs.vendor_id = v.id AND vs.status = "active"', 'left')
            ->groupBy('v.id');

        if ($type !== '') {
            $builder->where('v.type', $type);
        }
        if ($status !== '') {
            $builder->where('v.status', $status);
        }
        if ($search !== '') {
            $builder->groupStart()
                ->like('v.first_name', $search)
                ->orLike('v.last_name', $search)
                ->orLike('v.vendor_no', $search)
                ->orLike('v.business_name', $search)
                ->groupEnd();
        }

        return $builder->orderBy('v.last_name', 'ASC')->orderBy('v.first_name', 'ASC')->get()->getResultArray();
    }

    public function getDetail(int $id): ?array
    {
        return $this->find($id) ?: null;
    }

    public function getActiveForDropdown(string $type = ''): array
    {
        $builder = $this->where('status', 'active');
        if ($type !== '') {
            $builder->where('type', $type);
        }

        return $builder->orderBy('last_name', 'ASC')->orderBy('first_name', 'ASC')->findAll();
    }

    /**
     * Overdue arkalaba: active assignments without payment for the current billing period.
     */
    public function getOverdue(): array
    {
        $today            = date('Y-m-d');
        $twoDaysAgo       = date('Y-m-d', strtotime('-2 days'));
        $firstOfMonth     = date('Y-m-01');
        $firstOfLastMonth = date('Y-m-01', strtotime('first day of last month'));
        $lastOfLastMonth  = date('Y-m-t', strtotime('last month'));

        $stallRows = $this->db->table('vendor_stalls vs')
            ->select('v.id AS vendor_id, v.vendor_no,
                      CONCAT(v.first_name," ",v.last_name) AS vendor_name,
                      v.business_name, v.type AS vendor_type,
                      s.id AS stall_id, s.stall_code, s.section, s.type AS stall_type,
                      vs.id AS assignment_id, vs.assigned_date,
                      lp.period_end AS last_payment_date,
                      lp.payment_type AS last_payment_type,
                      DATEDIFF("' . $today . '", COALESCE(lp.period_end, vs.assigned_date)) AS days_overdue')
            ->join('vendors v', 'v.id = vs.vendor_id')
            ->join('stalls s', 's.id = vs.stall_id')
            ->join(
                '(SELECT p1.vendor_id, p1.stall_id, p1.period_end, p1.payment_type
                  FROM payments p1
                  INNER JOIN (
                      SELECT vendor_id, stall_id, MAX(period_end) AS max_end
                      FROM payments WHERE stall_id IS NOT NULL
                      GROUP BY vendor_id, stall_id
                  ) p2 ON p1.vendor_id = p2.vendor_id AND p1.stall_id = p2.stall_id AND p1.period_end = p2.max_end
                ) lp',
                'lp.vendor_id = vs.vendor_id AND lp.stall_id = vs.stall_id',
                'left'
            )
            ->where('vs.status', 'active')
            ->where('v.status', 'active')
            ->get()->getResultArray();

        $overdue = [];
        foreach ($stallRows as $row) {
            if ($this->isAssignmentOverdue($row, $today, $firstOfMonth, $firstOfLastMonth, $lastOfLastMonth, $twoDaysAgo)) {
                $overdue[] = $row;
            }
        }

        $ambulantRows = $this->getAmbulantOverdue($today, $twoDaysAgo);
        $overdue      = array_merge($overdue, $ambulantRows);

        usort($overdue, static fn ($a, $b) => ($b['days_overdue'] ?? 0) <=> ($a['days_overdue'] ?? 0));

        return $overdue;
    }

    private function isAssignmentOverdue(
        array $row,
        string $today,
        string $firstOfMonth,
        string $firstOfLastMonth,
        string $lastOfLastMonth,
        string $twoDaysAgo
    ): bool {
        $lastEnd  = $row['last_payment_date'] ?? null;
        $lastType = $row['last_payment_type'] ?? null;

        if ($lastEnd === null) {
            return ($row['assigned_date'] ?? $today) <= $twoDaysAgo;
        }

        if (in_array($lastType, ['daily', 'weekly'], true)) {
            return $lastEnd < $twoDaysAgo;
        }

        // Monthly (or unknown): paid for current month if period_end >= last day of current month
        // or period covers current month
        if ($lastEnd >= $firstOfMonth) {
            return false;
        }

        // Also check if they paid for last month (spec: monthly payers overdue if no last-month coverage)
        $paidLastMonth = ($lastEnd >= $firstOfLastMonth && $lastEnd <= $lastOfLastMonth);

        return ! $paidLastMonth && $lastEnd < $firstOfMonth;
    }

    private function getAmbulantOverdue(string $today, string $twoDaysAgo): array
    {
        return $this->db->table('vendors v')
            ->select('v.id AS vendor_id, v.vendor_no,
                      CONCAT(v.first_name," ",v.last_name) AS vendor_name,
                      v.business_name, v.type AS vendor_type,
                      NULL AS stall_id, "AMBU" AS stall_code, "Ambulant" AS section,
                      "ambulant" AS stall_type,
                      NULL AS assignment_id, v.created_at AS assigned_date,
                      MAX(p.period_end) AS last_payment_date,
                      MAX(p.payment_type) AS last_payment_type,
                      DATEDIFF("' . $today . '", COALESCE(MAX(p.period_end), DATE(v.created_at))) AS days_overdue')
            ->join('payments p', 'p.vendor_id = v.id AND p.stall_id IS NULL', 'left')
            ->where('v.type', 'ambulant')
            ->where('v.status', 'active')
            ->groupBy('v.id')
            ->having('last_payment_date IS NULL OR last_payment_date <', $twoDaysAgo)
            ->get()->getResultArray();
    }

    public function getExpiringPermits(int $days = 30): array
    {
        $today  = date('Y-m-d');
        $future = date('Y-m-d', strtotime("+{$days} days"));

        return $this->db->table('vendor_stalls vs')
            ->select('v.id AS vendor_id, v.vendor_no,
                      CONCAT(v.first_name," ",v.last_name) AS vendor_name,
                      s.id AS stall_id, s.stall_code, s.section,
                      vs.permit_no, vs.permit_expiry,
                      DATEDIFF(vs.permit_expiry, "' . $today . '") AS days_remaining')
            ->join('vendors v', 'v.id = vs.vendor_id')
            ->join('stalls s', 's.id = vs.stall_id')
            ->where('vs.status', 'active')
            ->where('vs.permit_expiry IS NOT NULL')
            ->where('vs.permit_expiry >=', $today)
            ->where('vs.permit_expiry <=', $future)
            ->orderBy('vs.permit_expiry', 'ASC')
            ->get()->getResultArray();
    }
}
