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
        // Remove foreign key constraint
        $this->db->query("ALTER TABLE users DROP FOREIGN KEY fk_users_role_id");
        
        // Remove role_id column
        $this->forge->dropColumn('users', 'role_id');
    }
}
