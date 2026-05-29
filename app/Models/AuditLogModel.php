<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table         = 'audit_logs';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['user_id', 'action', 'table_affected', 'record_id', 'created_at'];
    protected $returnType    = 'array';

    public function getLogsWithUsers(): array
    {
        return $this->select('audit_logs.*, users.name as user_name, users.role as user_role')
                    ->join('users', 'users.id = audit_logs.user_id', 'left')
                    ->orderBy('audit_logs.created_at', 'DESC')
                    ->findAll();
    }
}
