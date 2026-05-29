<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Fixes the users table schema:
 * 1. Expands the role ENUM to include manager and cashier.
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

        // Expand role ENUM to include all 4 roles
        $this->db->query("ALTER TABLE users MODIFY COLUMN `role` ENUM('admin','manager','staff','cashier') NOT NULL DEFAULT 'staff'");

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

        // Revert role ENUM to original two values
        $this->db->query("ALTER TABLE users MODIFY COLUMN `role` ENUM('admin','staff') NOT NULL");

        // Drop status column
        $fields = $this->db->getFieldNames('users');
        if (in_array('status', $fields)) {
            $this->forge->dropColumn('users', 'status');
        }
    }
}
