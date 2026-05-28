<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    /**
     * Returns all five stat counts for admin users.
     *
     * @return array<string, int|string>
     */
    public function getAdminStats(): array
    {
        return [
            'total_vendors'   => $this->countTable('vendors'),
            'total_stalls'    => $this->countTable('stalls'),
            'occupied_stalls' => $this->countWhere('stalls', 'status', 'occupied'),
            'vacant_stalls'   => $this->countWhere('stalls', 'status', 'vacant'),
            'total_records'   => $this->countTable('records'),
        ];
    }

    /**
     * Returns the two stat counts visible to staff users.
     *
     * @return array<string, int|string>
     */
    public function getStaffStats(): array
    {
        return [
            'total_vendors' => $this->countTable('vendors'),
            'total_stalls'  => $this->countTable('stalls'),
        ];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * COUNT(*) for an entire table. Returns 'N/A' on failure.
     *
     * @return int|string
     */
    private function countTable(string $table)
    {
        try {
            return (int) $this->db->table($table)->countAllResults();
        } catch (\Throwable $e) {
            log_message('error', "[DashboardModel] countTable({$table}) failed: {$e->getMessage()}");
            return 'N/A';
        }
    }

    /**
     * COUNT(*) with a single WHERE clause. Returns 'N/A' on failure.
     *
     * @return int|string
     */
    private function countWhere(string $table, string $column, string $value)
    {
        try {
            return (int) $this->db->table($table)->where($column, $value)->countAllResults();
        } catch (\Throwable $e) {
            log_message('error', "[DashboardModel] countWhere({$table}, {$column}={$value}) failed: {$e->getMessage()}");
            return 'N/A';
        }
    }
}
