<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUsersTableWithRoleId extends Migration
{
    public function up(): void
    {
        // Add role_id column
        $this->forge->addColumn('users', [
            'role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'email',
            ],
        ]);

        // Add foreign key constraint
        $this->db->query("
            ALTER TABLE users 
            ADD CONSTRAINT fk_users_role_id 
            FOREIGN KEY (role_id) REFERENCES roles(id) 
            ON DELETE SET NULL 
            ON UPDATE CASCADE
        ");

        // Migrate existing data: convert role enum to role_id
        // This will be handled by a seeder, but we'll keep the old column for now
    }

    public function down(): void
    {
        // Disable FK checks so we can drop the constraint safely
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        // Remove foreign key constraint if it exists
        $indexes = $this->db->query("
            SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'users'
              AND CONSTRAINT_NAME = 'fk_users_role_id'
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ")->getResultArray();

        if (! empty($indexes)) {
            $this->db->query('ALTER TABLE users DROP FOREIGN KEY fk_users_role_id');
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS=1');

        // Remove role_id column
        $fields = $this->db->getFieldNames('users');
        if (in_array('role_id', $fields)) {
            $this->forge->dropColumn('users', 'role_id');
        }
    }
}
