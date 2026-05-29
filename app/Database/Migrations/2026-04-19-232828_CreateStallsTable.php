<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateStallsTable extends Migration
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
            'stall_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'section' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['inside', 'outside', 'ambulant'],
                'null'       => false,
            ],
            'sqm' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'null'       => true,
                'default'    => null,
            ],
            'floor_level' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => null,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['occupied', 'vacant', 'suspended'],
                'null'       => false,
                'default'    => 'vacant',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('stall_code');
        $this->forge->createTable('stalls', true, ['ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4']);
    }

    public function down(): void
    {
        $this->forge->dropTable('stalls', true);
    }
}
