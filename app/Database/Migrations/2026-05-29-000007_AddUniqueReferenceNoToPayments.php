<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds a UNIQUE constraint on payments.reference_no to prevent
 * duplicate reference numbers at the database level.
 */
class AddUniqueReferenceNoToPayments extends Migration
{
    public function up(): void
    {
        // reference_no unique key is now included in CreatePaymentsTable (2026-04-19-232830).
        // This migration is kept for historical compatibility only.
        if (! $this->db->tableExists('payments')) {
            return;
        }

        $indexes = $this->db->getIndexData('payments');
        foreach ($indexes as $index) {
            if (in_array('reference_no', (array) $index->fields)) {
                return; // Already exists
            }
        }

        $this->db->query('ALTER TABLE payments ADD UNIQUE KEY `reference_no` (`reference_no`)');
    }

    public function down(): void
    {
        // Nothing to do — key is managed by CreatePaymentsTable
    }
}
