<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Fixes the vendors table schema to match the application.
 *
 * The original CreateVendorsTable migration created columns (contact_number, email, address)
 * that do not match what the application uses (stall_number, section, contact, permit_expiry).
 * This migration corrects the schema on existing databases.
 *
 * Safe to run on a fresh install — all changes use IF EXISTS / IF NOT EXISTS guards.
 */
class FixVendorsTableSchema extends Migration
{
    public function up(): void
    {
        // On a fresh install the correct schema is already in CreateVendorsTable — nothing to do
        if (! $this->db->tableExists('vendors')) {
            return;
        }

        // Add missing columns if they don't exist
        $fields = $this->db->getFieldNames('vendors');

        if (! in_array('stall_number', $fields)) {
            $this->forge->addColumn('vendors', [
                'stall_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'name',
                ],
            ]);
        }

        if (! in_array('section', $fields)) {
            $this->forge->addColumn('vendors', [
                'section' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Dry Goods', 'Wet Market', 'Livestock', 'Commercial'],
                    'null'       => true,
                    'after'      => 'stall_number',
                ],
            ]);
        }

        if (! in_array('contact', $fields)) {
            $this->forge->addColumn('vendors', [
                'contact' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                    'after'      => 'section',
                ],
            ]);
        }

        if (! in_array('permit_expiry', $fields)) {
            $this->forge->addColumn('vendors', [
                'permit_expiry' => [
                    'type' => 'DATE',
                    'null' => true,
                    'after' => 'contact',
                ],
            ]);
        }

        // Add unique key on stall_number if not already present
        $indexes = $this->db->getIndexData('vendors');
        $hasStallIndex = false;
        foreach ($indexes as $index) {
            if (in_array('stall_number', (array) $index->fields)) {
                $hasStallIndex = true;
                break;
            }
        }
        if (! $hasStallIndex) {
            $this->db->query('ALTER TABLE vendors ADD UNIQUE KEY `stall_number` (`stall_number`)');
        }
    }

    public function down(): void
    {
        if (! $this->db->tableExists('vendors')) {
            return;
        }

        // Reverse: drop the added columns
        $fields = $this->db->getFieldNames('vendors');

        if (in_array('stall_number', $fields)) {
            $this->forge->dropColumn('vendors', 'stall_number');
        }
        if (in_array('section', $fields)) {
            $this->forge->dropColumn('vendors', 'section');
        }
        if (in_array('contact', $fields)) {
            $this->forge->dropColumn('vendors', 'contact');
        }
        if (in_array('permit_expiry', $fields)) {
            $this->forge->dropColumn('vendors', 'permit_expiry');
        }
    }
}
