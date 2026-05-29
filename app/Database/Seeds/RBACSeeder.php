<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RBACSeeder extends Seeder
{
    public function run(): void
    {
        // Insert roles with hierarchy levels (matches WBMM roles)
        $roles = [
            [
                'role_name'  => 'admin',
                'description' => 'Full system access',
                'level'      => 100,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role_name'  => 'supervisor',
                'description' => 'Can view reports and oversight dashboards',
                'level'      => 80,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role_name'  => 'collector',
                'description' => 'Can record arkalaba payments and view own records',
                'level'      => 60,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role_name'  => 'staff',
                'description' => 'Can register vendors and manage assignments with limited access',
                'level'      => 50,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('roles')->emptyTable();
        $this->db->table('roles')->insertBatch($roles);

        // Get role IDs
        $adminRole = $this->db->table('roles')->where('role_name', 'admin')->get()->getRowArray();
        $supervisorRole = $this->db->table('roles')->where('role_name', 'supervisor')->get()->getRowArray();
        $collectorRole = $this->db->table('roles')->where('role_name', 'collector')->get()->getRowArray();
        $staffRole = $this->db->table('roles')->where('role_name', 'staff')->get()->getRowArray();

        // Insert permissions mapped to WBMM modules
        $permissions = [
            ['permission_name' => 'manage_users',    'description' => 'Can manage user accounts and roles'],
            ['permission_name' => 'manage_vendors',  'description' => 'Can register, edit, and delete vendors'],
            ['permission_name' => 'manage_stalls',   'description' => 'Can create, edit, and delete stalls'],
            ['permission_name' => 'record_payments', 'description' => 'Can record rental payments'],
            ['permission_name' => 'view_records',    'description' => 'Can view records and reports'],
            ['permission_name' => 'export_records',  'description' => 'Can export CSV reports'],
            ['permission_name' => 'view_audit_logs', 'description' => 'Can view system audit logs'],
        ];

        $this->db->table('permissions')->emptyTable();
        $this->db->table('permissions')->insertBatch($permissions);

        // Get permission IDs
        $permissions = $this->db->table('permissions')->get()->getResultArray();
        $permMap = [];
        foreach ($permissions as $perm) {
            $permMap[$perm['permission_name']] = $perm['id'];
        }

        // Assign permissions to roles
        $rolePermissions = [];

        // Admin: All permissions
        foreach ($permMap as $permId) {
            $rolePermissions[] = [
                'role_id' => $adminRole['id'],
                'permission_id' => $permId,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        // Supervisor: View/export records (reports), no user/stall management
        $supervisorPerms = ['view_records', 'export_records'];
        foreach ($supervisorPerms as $permName) {
            $rolePermissions[] = [
                'role_id' => $supervisorRole['id'],
                'permission_id' => $permMap[$permName],
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        // Staff: Vendors + assignments (modeled as manage_vendors) + view records
        $staffPerms = ['manage_vendors', 'view_records'];
        foreach ($staffPerms as $permName) {
            $rolePermissions[] = [
                'role_id' => $staffRole['id'],
                'permission_id' => $permMap[$permName],
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        // Collector: Record payments + view records (own records are enforced by application logic)
        $rolePermissions[] = [
            'role_id' => $collectorRole['id'],
            'permission_id' => $permMap['record_payments'],
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $rolePermissions[] = [
            'role_id' => $collectorRole['id'],
            'permission_id' => $permMap['view_records'],
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('role_permissions')->emptyTable();
        $this->db->table('role_permissions')->insertBatch($rolePermissions);

        // Update existing users to use role_id
        $userFields = $this->db->getFieldNames('users');
        if (! in_array('role_id', $userFields, true)) {
            echo "Skipped updating users.role_id (column does not exist)\n";
            echo "RBAC Seeder completed successfully.\n";
            echo "Created 4 roles: admin, supervisor, collector, staff\n";
            echo "Created 7 permissions\n";
            echo "Assigned permissions to roles based on hierarchy\n";
            return;
        }

        $users = $this->db->table('users')->get()->getResultArray();
        
        foreach ($users as $user) {
            $roleId = null;

            switch (strtolower($user['role'])) {
                case 'admin':
                    $roleId = $adminRole['id'];
                    break;
                case 'supervisor':
                    $roleId = $supervisorRole['id'];
                    break;
                case 'collector':
                    $roleId = $collectorRole['id'];
                    break;
                case 'staff':
                default:
                    $roleId = $staffRole['id'];
            }

            $this->db->table('users')->where('id', $user['id'])->update(['role_id' => $roleId]);
        }

        echo "RBAC Seeder completed successfully.\n";
        echo "Created 4 roles: admin, supervisor, collector, staff\n";
        echo "Created 7 permissions\n";
        echo "Assigned permissions to roles based on hierarchy\n";
        echo "Updated existing users with role_id\n";
    }
}
