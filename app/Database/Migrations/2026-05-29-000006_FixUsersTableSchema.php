<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Fixes the users table schema:
 * 1. Ensures the role ENUM matches the WBMM application roles.
 * 2. Adds the status column if missing.
 */
class FixUsersTableSchema extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('users')) {
            return;
        }

        $fields = $this->db->getFieldNames('users');

        // Ensure role ENUM matches the application (admin/supervisor/collector/staff)
        $this->db->query("ALTER TABLE users MODIFY COLUMN `role` ENUM('admin','supervisor','collector','staff') NOT NULL DEFAULT 'staff'");

        // Add status column if missing
        if (! in_array('status', $fields)) {
            $this->forge->addColumn('users', [
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['active', 'inactive'],
                    'null'       => false,
                    'default'    => 'active',
                    'after'      => 'role',
                ],
            ]);
        }
    }

    public function down(): void
    {
        if (! $this->db->tableExists('users')) {
            return;
        }

        // Best-effort revert to a minimal set (legacy)
        $this->db->query("ALTER TABLE users MODIFY COLUMN `role` ENUM('admin','staff') NOT NULL DEFAULT 'staff'");

        // Drop status column
        $fields = $this->db->getFieldNames('users');
        if (in_array('status', $fields)) {
            $this->forge->dropColumn('users', 'status');
        }
    }
}
