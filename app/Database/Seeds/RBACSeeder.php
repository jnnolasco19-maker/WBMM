<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RBACSeeder extends Seeder
{
    public function run(): void
    {
        // Insert roles with hierarchy levels
        $roles = [
            [
                'role_name'  => 'admin',
                'description' => 'Full system access - can manage users, roles, products, orders, and reports',
                'level'      => 100,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role_name'  => 'manager',
                'description' => 'Can manage products, inventory, and orders. Can view reports. Cannot manage users/roles.',
                'level'      => 75,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role_name'  => 'staff',
                'description' => 'Can add and update products. Can manage inventory. Cannot access user management or reports.',
                'level'      => 50,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role_name'  => 'cashier',
                'description' => 'Can view products and create orders/sales. Has limited dashboard access.',
                'level'      => 25,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('roles')->emptyTable();
        $this->db->table('roles')->insertBatch($roles);

        // Get role IDs
        $adminRole = $this->db->table('roles')->where('role_name', 'admin')->get()->getRowArray();
        $managerRole = $this->db->table('roles')->where('role_name', 'manager')->get()->getRowArray();
        $staffRole = $this->db->table('roles')->where('role_name', 'staff')->get()->getRowArray();
        $cashierRole = $this->db->table('roles')->where('role_name', 'cashier')->get()->getRowArray();

        // Insert permissions (optional advanced feature)
        $permissions = [
            ['permission_name' => 'manage_users', 'description' => 'Can manage users and roles'],
            ['permission_name' => 'manage_products', 'description' => 'Can manage products'],
            ['permission_name' => 'manage_inventory', 'description' => 'Can manage inventory'],
            ['permission_name' => 'manage_orders', 'description' => 'Can manage orders'],
            ['permission_name' => 'view_reports', 'description' => 'Can view reports'],
            ['permission_name' => 'create_orders', 'description' => 'Can create new orders'],
            ['permission_name' => 'delete_records', 'description' => 'Can delete records'],
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

        // Manager: Most permissions except user management
        $managerPerms = ['manage_products', 'manage_inventory', 'manage_orders', 'view_reports'];
        foreach ($managerPerms as $permName) {
            $rolePermissions[] = [
                'role_id' => $managerRole['id'],
                'permission_id' => $permMap[$permName],
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        // Staff: Product and inventory management
        $staffPerms = ['manage_products', 'manage_inventory'];
        foreach ($staffPerms as $permName) {
            $rolePermissions[] = [
                'role_id' => $staffRole['id'],
                'permission_id' => $permMap[$permName],
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        // Cashier: Order creation only
        $rolePermissions[] = [
            'role_id' => $cashierRole['id'],
            'permission_id' => $permMap['create_orders'],
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('role_permissions')->emptyTable();
        $this->db->table('role_permissions')->insertBatch($rolePermissions);

        // Update existing users to use role_id
        $users = $this->db->table('users')->get()->getResultArray();
        
        foreach ($users as $user) {
            $roleId = null;
            
            // Map old role enum to new role_id
            switch (strtolower($user['role'])) {
                case 'admin':
                    $roleId = $adminRole['id'];
                    break;
                case 'staff':
                    $roleId = $staffRole['id'];
                    break;
                default:
                    // Default to staff if unknown
                    $roleId = $staffRole['id'];
            }
            
            $this->db->table('users')->where('id', $user['id'])->update(['role_id' => $roleId]);
        }

        echo "RBAC Seeder completed successfully.\n";
        echo "Created 4 roles: admin, manager, staff, cashier\n";
        echo "Created 7 permissions\n";
        echo "Assigned permissions to roles based on hierarchy\n";
        echo "Updated existing users with role_id\n";
    }
}
