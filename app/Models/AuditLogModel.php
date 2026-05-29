<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table         = 'audit_logs';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'user_id', 'action', 'table_affected', 'record_id', 'details',
    ];
    protected $useTimestamps = false;
    protected $returnType    = 'array';

    public function log(
        string  $action,
        string  $table,
        ?int    $recordId = null,
        string  $details  = ''
    ): void {
        $this->insert([
            'user_id'        => session()->get('user_id'),
            'action'         => $action,
            'table_affected' => $table,
            'record_id'      => $recordId,
            'details'        => $details,
        ]);
    }

    public function getRecent(int $limit = 50): array
    {
        return $this->db->table('audit_logs a')
            ->select('a.*, u.name AS user_name, u.role AS user_role')
            ->join('users u', 'u.id = a.user_id', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }
}
