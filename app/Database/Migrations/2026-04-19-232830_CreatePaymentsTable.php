<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePaymentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'reference_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'vendor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'stall_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'rate_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'payment_type' => [
                'type'       => 'ENUM',
                'constraint' => ['daily', 'weekly', 'monthly'],
                'null'       => false,
            ],
            'sqm_charged' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'null'       => true,
                'default'    => null,
            ],
            'rate_used' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'computed_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'amount_paid' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'period_start' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'period_end' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'collected_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'payment_date' => [
                'type'    => 'TIMESTAMP',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('reference_no');
        $this->forge->addForeignKey('vendor_id', 'vendors', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('stall_id', 'stalls', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('rate_id', 'rates', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('collected_by', 'users', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('payments', true, ['ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4']);
    }

    public function down(): void
    {
        $this->forge->dropTable('payments', true);
    }
}
