<?php

namespace App\Models;

use CodeIgniter\Model;

class RateModel extends Model
{
    protected $table         = 'rates';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'inside_rate_per_sqm', 'outside_daily_rate',
        'outside_weekly_rate', 'outside_monthly_rate',
        'ambulant_daily_rate', 'effective_date', 'created_by',
    ];
    protected $useTimestamps = false;
    protected $returnType    = 'array';

    // ── Current active rate (latest effective_date <= today) ─

    public function getCurrent(): ?array
    {
        return $this->db->table('rates r')
            ->select('r.*, u.name AS created_by_name')
            ->join('users u', 'u.id = r.created_by', 'left')
            ->where('r.effective_date <=', date('Y-m-d'))
            ->orderBy('r.effective_date', 'DESC')
            ->orderBy('r.id', 'DESC')
            ->limit(1)
            ->get()->getRowArray() ?: null;
    }

    // ── All rates with usage count ───────────────────────────

    public function getAllWithUsage(): array
    {
        return $this->db->table('rates r')
            ->select('r.*, u.name AS created_by_name,
                      COUNT(p.id) AS payment_count')
            ->join('users u',    'u.id = r.created_by', 'left')
            ->join('payments p', 'p.rate_id = r.id',    'left')
            ->groupBy('r.id')
            ->orderBy('r.effective_date', 'DESC')
            ->get()->getResultArray();
    }

    // ── Compute amount based on stall type and payment type ──

    public function compute(array $rate, string $stallType, string $paymentType, float $sqm = 0): float
    {
        if ($stallType === 'inside') {
            $daily = $sqm * (float) $rate['inside_rate_per_sqm'];
            return match ($paymentType) {
                'daily'   => round($daily, 2),
                default   => round($daily * 30, 2),
            };
        }

        if ($stallType === 'outside') {
            $daily = $sqm * (float) $rate['outside_monthly_rate'];
            return match ($paymentType) {
                'daily'   => round($daily, 2),
                default   => round($daily * 30, 2),
            };
        }

        // ambulant — daily only
        return (float) $rate['ambulant_daily_rate'];
    }
}
