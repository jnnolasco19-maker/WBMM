<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\CLI\CLI;

class AuthSeeder extends Seeder
{
    public function run(): void
    {
        // role_id is optional (only present if RBAC migrations were executed)
        $roleIds = [
            'admin'      => null,
            'supervisor' => null,
            'collector'  => null,
            'staff'      => null,
        ];

        if ($this->db->tableExists('roles')) {
            $roles = $this->db->table('roles')->get()->getResultArray();
            foreach ($roles as $r) {
                $name = strtolower((string) ($r['role_name'] ?? ''));
                if (array_key_exists($name, $roleIds)) {
                    $roleIds[$name] = $r['id'] ?? null;
                }
            }
        }

        $data = [
            [
                'name'     => 'Market Administrator',
                'email'    => 'admin@wbmm.com',
                'password' => password_hash('Admin@1234', PASSWORD_BCRYPT),
                'role'     => 'admin',
                'role_id'  => $roleIds['admin'],
                'status'   => 'active',
            ],
            [
                'name'     => 'Maria Supervisor',
                'email'    => 'supervisor@wbmm.com',
                'password' => password_hash('Admin@1234', PASSWORD_BCRYPT),
                'role'     => 'supervisor',
                'role_id'  => $roleIds['supervisor'],
                'status'   => 'active',
            ],
            [
                'name'     => 'Juan Maningil',
                'email'    => 'collector1@wbmm.com',
                'password' => password_hash('Admin@1234', PASSWORD_BCRYPT),
                'role'     => 'collector',
                'role_id'  => $roleIds['collector'],
                'status'   => 'active',
            ],
            [
                'name'     => 'Pedro Tigsingil',
                'email'    => 'collector2@wbmm.com',
                'password' => password_hash('Admin@1234', PASSWORD_BCRYPT),
                'role'     => 'collector',
                'role_id'  => $roleIds['collector'],
                'status'   => 'active',
            ],
            [
                'name'     => 'Ana Staff',
                'email'    => 'staff@wbmm.com',
                'password' => password_hash('Admin@1234', PASSWORD_BCRYPT),
                'role'     => 'staff',
                'role_id'  => $roleIds['staff'],
                'status'   => 'active',
            ],
        ];

        $usersTable = $this->db->table('users');

        // If role_id column doesn't exist (no RBAC migration), drop it from the payload
        $userFields = $this->db->getFieldNames('users');
        if (! in_array('role_id', $userFields, true)) {
            foreach ($data as &$row) {
                unset($row['role_id']);
            }
            unset($row);
        }

        $this->db->disableForeignKeyChecks();
        $usersTable->truncate();
        $usersTable->insertBatch($data);
        $this->db->enableForeignKeyChecks();

        CLI::write("Auth Seeder completed successfully.", 'green');
        CLI::write("Created 5 test users:");
        CLI::write("  - admin@wbmm.com / Admin@1234");
        CLI::write("  - supervisor@wbmm.com / Admin@1234");
        CLI::write("  - collector1@wbmm.com / Admin@1234");
        CLI::write("  - collector2@wbmm.com / Admin@1234");
        CLI::write("  - staff@wbmm.com / Admin@1234");
    }
}
