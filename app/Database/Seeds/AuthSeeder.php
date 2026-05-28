<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AuthSeeder extends Seeder
{
    public function run(): void
    {
        // Get role IDs from roles table
        $adminRole = $this->db->table('roles')->where('role_name', 'admin')->get()->getRowArray();
        $managerRole = $this->db->table('roles')->where('role_name', 'manager')->get()->getRowArray();
        $staffRole = $this->db->table('roles')->where('role_name', 'staff')->get()->getRowArray();
        $cashierRole = $this->db->table('roles')->where('role_name', 'cashier')->get()->getRowArray();

        $data = [
            [
                'name'       => 'Admin User',
                'email'      => 'admin@wbmm.com',
                'password'   => password_hash('Admin@1234', PASSWORD_BCRYPT),
                'role'       => 'admin',
                'role_id'    => $adminRole['id'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Manager User',
                'email'      => 'manager@wbmm.com',
                'password'   => password_hash('Manager@1234', PASSWORD_BCRYPT),
                'role'       => 'manager',
                'role_id'    => $managerRole['id'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Staff Member',
                'email'      => 'staff@wbmm.com',
                'password'   => password_hash('Staff@1234', PASSWORD_BCRYPT),
                'role'       => 'staff',
                'role_id'    => $staffRole['id'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Cashier User',
                'email'      => 'cashier@wbmm.com',
                'password'   => password_hash('Cashier@1234', PASSWORD_BCRYPT),
                'role'       => 'cashier',
                'role_id'    => $cashierRole['id'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $this->db->table('users')->emptyTable();
        $this->db->table('users')->insertBatch($data);
        
        echo "Auth Seeder completed successfully.\n";
        echo "Created 4 test users:\n";
        echo "  - admin@wbmm.com / Admin@1234\n";
        echo "  - manager@wbmm.com / Manager@1234\n";
        echo "  - staff@wbmm.com / Staff@1234\n";
        echo "  - cashier@wbmm.com / Cashier@1234\n";
    }
}
