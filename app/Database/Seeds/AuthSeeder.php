<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\CLI\CLI;

class AuthSeeder extends Seeder
{
    public function run(): void
    {
        $rolesTable = $this->db->table('roles');

        // Get role IDs safely
        $adminRole   = $rolesTable->where('role_name', 'admin')->get()->getRowArray();
        $managerRole = $rolesTable->where('role_name', 'manager')->get()->getRowArray();
        $staffRole   = $rolesTable->where('role_name', 'staff')->get()->getRowArray();
        $cashierRole = $rolesTable->where('role_name', 'cashier')->get()->getRowArray();

        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'name'       => 'Admin User',
                'email'      => 'admin@wbmm.com',
                'password'   => password_hash('Admin@1234', PASSWORD_BCRYPT),
                'role'       => 'admin',
                'role_id'   => $adminRole['id'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Manager User',
                'email'      => 'manager@wbmm.com',
                'password'   => password_hash('Manager@1234', PASSWORD_BCRYPT),
                'role'       => 'manager',
                'role_id'   => $managerRole['id'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Staff Member',
                'email'      => 'staff@wbmm.com',
                'password'   => password_hash('Staff@1234', PASSWORD_BCRYPT),
                'role'       => 'staff',
                'role_id'   => $staffRole['id'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Cashier User',
                'email'      => 'cashier@wbmm.com',
                'password'   => password_hash('Cashier@1234', PASSWORD_BCRYPT),
                'role'       => 'cashier',
                'role_id'   => $cashierRole['id'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $usersTable = $this->db->table('users');

        // Proper way to clear table in CI4
        $usersTable->truncate();

        $usersTable->insertBatch($data);

        CLI::write("Auth Seeder completed successfully.", 'green');
        CLI::write("Created 4 test users:");
        CLI::write("  - admin@wbmm.com / Admin@1234");
        CLI::write("  - manager@wbmm.com / Manager@1234");
        CLI::write("  - staff@wbmm.com / Staff@1234");
        CLI::write("  - cashier@wbmm.com / Cashier@1234");
    }
}