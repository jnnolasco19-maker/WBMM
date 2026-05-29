<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table         = 'payments';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'reference_no', 'vendor_id', 'stall_id', 'rate_id',
        'payment_type', 'sqm_charged', 'rate_used',
        'computed_amount', 'amount_paid', 'period_start',
        'period_end', 'collected_by', 'notes',
    ];
    protected $useTimestamps = false;
    protected $returnType    = 'array';

    public function generateReferenceNo(): string
    {
        $today  = date('Ymd');
        $prefix = 'ARK-' . $today . '-';

        $count = $this->db->table('payments')
            ->like('reference_no', $prefix, 'after')
            ->countAllResults();

        return $prefix . str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
    }

    public function getDetail(int $id): ?array
    {
        return $this->db->table('payments p')
            ->select('p.*,
                      CONCAT(v.first_name," ",v.last_name) AS vendor_name,
                      v.vendor_no, v.business_name, v.type AS vendor_type,
                      s.stall_code, s.section, s.type AS stall_type, s.sqm,
                      u.name AS collector_name,
                      r.inside_rate_per_sqm, r.effective_date AS rate_effective_date')
            ->join('vendors v', 'v.id = p.vendor_id')
            ->join('stalls s', 's.id = p.stall_id', 'left')
            ->join('users u', 'u.id = p.collected_by')
            ->join('rates r', 'r.id = p.rate_id')
            ->where('p.id', $id)
            ->get()->getRowArray() ?: null;
    }

    public function applyFilters(
        string $search = '',
        string $stallType = '',
        string $paymentType = '',
        string $collectedBy = '',
        string $dateFrom = '',
        string $dateTo = '',
        string $vendorId = '',
        ?int $collectorOnly = null
    ): self {
        $this->select('payments.*,
                      CONCAT(v.first_name," ",v.last_name) AS vendor_name,
                      v.vendor_no,
                      s.stall_code, s.section, s.type AS stall_type,
                      u.name AS collector_name')
            ->join('vendors v', 'v.id = payments.vendor_id')
            ->join('stalls s', 's.id = payments.stall_id', 'left')
            ->join('users u', 'u.id = payments.collected_by');

        if ($search !== '') {
            $this->groupStart()
                ->like('v.first_name', $search)
                ->orLike('v.last_name', $search)
                ->orLike('v.vendor_no', $search)
                ->orLike('payments.reference_no', $search)
                ->orLike('s.stall_code', $search)
                ->groupEnd();
        }
        if ($stallType !== '') {
            $this->where('s.type', $stallType);
        }
        if ($paymentType !== '') {
            $this->where('payments.payment_type', $paymentType);
        }
        if ($collectedBy !== '') {
            $this->where('payments.collected_by', (int) $collectedBy);
        }
        if ($vendorId !== '') {
            $this->where('payments.vendor_id', (int) $vendorId);
        }
        if ($dateFrom !== '') {
            $this->where('DATE(payments.payment_date) >=', $dateFrom);
        }
        if ($dateTo !== '') {
            $this->where('DATE(payments.payment_date) <=', $dateTo);
        }
        if ($collectorOnly !== null) {
            $this->where('payments.collected_by', $collectorOnly);
        }

        $this->orderBy('payments.payment_date', 'DESC');

        return $this;
    }

    public function getFiltered(
        string $search = '',
        string $stallType = '',
        string $paymentType = '',
        string $collectedBy = '',
        string $dateFrom = '',
        string $dateTo = '',
        string $vendorId = '',
        ?int $collectorOnly = null
    ): array {
        return $this->applyFilters($search, $stallType, $paymentType, $collectedBy, $dateFrom, $dateTo, $vendorId, $collectorOnly)
            ->findAll();
    }

    public function getByVendor(int $vendorId, int $limit = 0): array
    {
        $builder = $this->db->table('payments p')
            ->select('p.*, s.stall_code, s.section, s.type AS stall_type, u.name AS collector_name')
            ->join('stalls s', 's.id = p.stall_id', 'left')
            ->join('users u', 'u.id = p.collected_by')
            ->where('p.vendor_id', $vendorId)
            ->orderBy('p.payment_date', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        return $builder->get()->getResultArray();
    }

    public function getByStall(int $stallId): array
    {
        return $this->db->table('payments p')
            ->select('p.*, CONCAT(v.first_name," ",v.last_name) AS vendor_name, v.vendor_no, u.name AS collector_name')
            ->join('vendors v', 'v.id = p.vendor_id')
            ->join('users u', 'u.id = p.collected_by')
            ->where('p.stall_id', $stallId)
            ->orderBy('p.payment_date', 'DESC')
            ->get()->getResultArray();
    }

    public function getMonthlyTotals(): array
    {
        $rows = $this->db->table('payments p')
            ->select("DATE_FORMAT(p.payment_date,'%Y-%m') AS month,
                      COALESCE(s.type, 'ambulant') AS stall_type,
                      SUM(p.amount_paid) AS total")
            ->join('stalls s', 's.id = p.stall_id', 'left')
            ->where('p.payment_date >=', date('Y-m-01', strtotime('-5 months')))
            ->groupBy('month, stall_type')
            ->orderBy('month', 'ASC')
            ->get()->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['month']][$row['stall_type']] = (float) $row['total'];
        }

        return $result;
    }

    public function getTotalThisMonth(): float
    {
        $row = $this->db->table('payments')
            ->selectSum('amount_paid', 'total')
            ->where('MONTH(payment_date)', date('m'))
            ->where('YEAR(payment_date)', date('Y'))
            ->get()->getRowArray();

        return (float) ($row['total'] ?? 0);
    }

    public function getCollectorSummary(): array
    {
        return $this->db->table('payments p')
            ->select('u.id AS collector_id, u.name AS collector_name,
                      COUNT(CASE WHEN DATE(p.payment_date) = CURDATE() THEN 1 END) AS collections_today,
                      COUNT(CASE WHEN YEARWEEK(p.payment_date,1) = YEARWEEK(CURDATE(),1) THEN 1 END) AS collections_week,
                      COUNT(CASE WHEN MONTH(p.payment_date)=MONTH(CURDATE()) AND YEAR(p.payment_date)=YEAR(CURDATE()) THEN 1 END) AS collections_month,
                      SUM(p.computed_amount) AS total_computed,
                      SUM(p.amount_paid) AS total_paid,
                      SUM(p.amount_paid - p.computed_amount) AS difference')
            ->join('users u', 'u.id = p.collected_by')
            ->where('u.role', 'collector')
            ->groupBy('u.id')
            ->orderBy('u.name', 'ASC')
            ->get()->getResultArray();
    }

    public function getByCollector(int $collectorId): array
    {
        return $this->db->table('payments p')
            ->select('p.*, CONCAT(v.first_name," ",v.last_name) AS vendor_name, v.vendor_no,
                      s.stall_code, s.section, s.type AS stall_type')
            ->join('vendors v', 'v.id = p.vendor_id')
            ->join('stalls s', 's.id = p.stall_id', 'left')
            ->where('p.collected_by', $collectorId)
            ->orderBy('p.payment_date', 'DESC')
            ->get()->getResultArray();
    }

    public function getRecent(int $limit = 10): array
    {
        return $this->db->table('payments p')
            ->select('p.*, CONCAT(v.first_name," ",v.last_name) AS vendor_name, v.vendor_no,
                      s.stall_code, COALESCE(s.type, v.type) AS stall_type, u.name AS collector_name')
            ->join('vendors v', 'v.id = p.vendor_id')
            ->join('stalls s', 's.id = p.stall_id', 'left')
            ->join('users u', 'u.id = p.collected_by')
            ->orderBy('p.payment_date', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    public function getSummaryByStallType(): array
    {
        return $this->db->table('payments p')
            ->select('COALESCE(s.type, "ambulant") AS group_key,
                      SUM(p.computed_amount) AS total_computed,
                      SUM(p.amount_paid) AS total_paid,
                      COUNT(p.id) AS txn_count')
            ->join('stalls s', 's.id = p.stall_id', 'left')
            ->groupBy('group_key')
            ->get()->getResultArray();
    }

    public function getSummaryBySection(): array
    {
        return $this->db->table('payments p')
            ->select('COALESCE(s.section, "Ambulant") AS group_key,
                      SUM(p.computed_amount) AS total_computed,
                      SUM(p.amount_paid) AS total_paid,
                      COUNT(p.id) AS txn_count')
            ->join('stalls s', 's.id = p.stall_id', 'left')
            ->groupBy('group_key')
            ->orderBy('group_key', 'ASC')
            ->get()->getResultArray();
    }

    public function getSummaryByMonth(int $months = 6): array
    {
        return $this->db->table('payments p')
            ->select("DATE_FORMAT(p.payment_date,'%Y-%m') AS group_key,
                      SUM(p.computed_amount) AS total_computed,
                      SUM(p.amount_paid) AS total_paid,
                      COUNT(p.id) AS txn_count")
            ->where('p.payment_date >=', date('Y-m-01', strtotime('-' . ($months - 1) . ' months')))
            ->groupBy('group_key')
            ->orderBy('group_key', 'DESC')
            ->get()->getResultArray();
    }
}
