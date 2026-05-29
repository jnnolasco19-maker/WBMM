<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Best-effort compatibility migration for older vendor schemas.
 *
 * The WBMM application expects the vendors table to have:
 * - vendor_no, first_name, last_name, business_name, contact, address, id_type, id_number
 * - type (inside/outside/ambulant), status (active/inactive/suspended), created_at
 *
 * Safe to run on a fresh install (it will detect the correct schema and do nothing).
 */
class FixVendorsTableSchema extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('vendors')) {
            return;
        }

        $fields = $this->db->getFieldNames('vendors');

        // If we're already on the expected schema, do nothing.
        if (in_array('vendor_no', $fields, true)
            && in_array('first_name', $fields, true)
            && in_array('last_name', $fields, true)
            && in_array('type', $fields, true)
        ) {
            return;
        }

        // Add missing columns (nullable) so the application can run.
        $add = [];

        if (! in_array('vendor_no', $fields, true)) {
            $add['vendor_no'] = ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true];
        }
        if (! in_array('first_name', $fields, true)) {
            $add['first_name'] = ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true];
        }
        if (! in_array('last_name', $fields, true)) {
            $add['last_name'] = ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true];
        }
        if (! in_array('business_name', $fields, true)) {
            $add['business_name'] = ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true];
        }
        if (! in_array('contact', $fields, true)) {
            $add['contact'] = ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true];
        }
        if (! in_array('address', $fields, true)) {
            $add['address'] = ['type' => 'TEXT', 'null' => true];
        }
        if (! in_array('id_type', $fields, true)) {
            $add['id_type'] = ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true];
        }
        if (! in_array('id_number', $fields, true)) {
            $add['id_number'] = ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true];
        }
        if (! in_array('type', $fields, true)) {
            // Use VARCHAR here for compatibility; fresh installs use ENUM in CreateVendorsTable.
            $add['type'] = ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true];
        }
        if (! in_array('status', $fields, true)) {
            $add['status'] = ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true];
        }
        if (! in_array('created_at', $fields, true)) {
            $add['created_at'] = ['type' => 'DATETIME', 'null' => true];
        }

        if ($add !== []) {
            $this->forge->addColumn('vendors', $add);
        }

        // Backfill some values where possible.
        $fields = $this->db->getFieldNames('vendors');

        if (in_array('name', $fields, true) && in_array('first_name', $fields, true)) {
            $this->db->query("UPDATE vendors SET first_name = COALESCE(first_name, name) WHERE first_name IS NULL OR first_name = ''");
        }

        if (in_array('vendor_no', $fields, true)) {
            $this->db->query("UPDATE vendors SET vendor_no = COALESCE(vendor_no, CONCAT('VND-LEGACY-', LPAD(id, 4, '0'))) WHERE vendor_no IS NULL OR vendor_no = ''");
        }

        if (in_array('type', $fields, true)) {
            $this->db->query("UPDATE vendors SET type = COALESCE(type, 'inside') WHERE type IS NULL OR type = ''");
        }

        if (in_array('status', $fields, true)) {
            $this->db->query("UPDATE vendors SET status = COALESCE(status, 'active') WHERE status IS NULL OR status = ''");
        }

        // Add unique key on vendor_no if not already present
        $indexes = $this->db->getIndexData('vendors');
        foreach ($indexes as $index) {
            if (in_array('vendor_no', (array) $index->fields, true)) {
                return;
            }
        }
        $this->db->query('ALTER TABLE vendors ADD UNIQUE KEY `vendor_no` (`vendor_no`)');
    }

    public function down(): void
    {
        if (! $this->db->tableExists('vendors')) {
            return;
        }
        // No-op (dropping columns is risky on legacy databases)
    }
}
