<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Database\RawSql;

/**
 * Seeds a fresh WBMM database with the same demo data as schema.sql.
 *
 * Usage:
 *   php spark db:seed WBMMSeeder
 *
 * Recommended fresh install:
 *   php spark migrate:fresh
 *   php spark db:seed WBMMSeeder
 */
class WBMMSeeder extends Seeder
{
    public function run(): void
    {
        // Optional RBAC tables (roles/permissions/role_permissions + role_id)
        if ($this->db->tableExists('roles') && $this->db->tableExists('permissions') && $this->db->tableExists('role_permissions')) {
            $this->call(RBACSeeder::class);
        }

        // Always seed the default login accounts
        $this->call(AuthSeeder::class);

        // Require core tables
        foreach (['rates', 'stalls', 'vendors', 'vendor_stalls', 'payments'] as $t) {
            if (! $this->db->tableExists($t)) {
                throw new \RuntimeException("Missing table '{$t}'. Did you run migrations?");
            }
        }

        // Truncate demo tables in FK-safe order
        $this->db->disableForeignKeyChecks();
        foreach (['payments', 'vendor_stalls', 'vendors', 'stalls', 'rates'] as $t) {
            $this->db->table($t)->truncate();
        }
        $this->db->enableForeignKeyChecks();

        // Lookup seeded users
        $adminId      = (int) ($this->db->table('users')->select('id')->where('email', 'admin@wbmm.com')->get()->getRowArray()['id'] ?? 1);
        $collector1Id = (int) ($this->db->table('users')->select('id')->where('email', 'collector1@wbmm.com')->get()->getRowArray()['id'] ?? 1);
        $collector2Id = (int) ($this->db->table('users')->select('id')->where('email', 'collector2@wbmm.com')->get()->getRowArray()['id'] ?? 1);

        // --- rates ---
        $today = date('Y-m-d');
        $this->db->table('rates')->insert([
            'inside_rate_per_sqm'  => 45.00,
            'outside_daily_rate'   => 25.00,
            'outside_weekly_rate'  => 150.00,
            'outside_monthly_rate' => 500.00,
            'ambulant_daily_rate'  => 15.00,
            'effective_date'       => $today,
            'created_by'           => $adminId,
            'created_at'           => new RawSql('CURRENT_TIMESTAMP'),
        ]);
        $rateId = (int) $this->db->insertID();

        // --- stalls ---
        $this->db->table('stalls')->insertBatch([
            ['stall_code' => 'A-101',         'section' => 'Dry Goods',     'type' => 'inside',   'sqm' => 6.00,  'floor_level' => 'Ground Floor', 'status' => 'vacant'],
            ['stall_code' => 'A-102',         'section' => 'Dry Goods',     'type' => 'inside',   'sqm' => 4.50,  'floor_level' => 'Ground Floor', 'status' => 'vacant'],
            ['stall_code' => 'A-201',         'section' => 'Dry Goods',     'type' => 'inside',   'sqm' => 6.00,  'floor_level' => '2nd Floor',    'status' => 'vacant'],
            ['stall_code' => 'WM-001',        'section' => 'Wet Market',    'type' => 'inside',   'sqm' => 8.00,  'floor_level' => 'Ground Floor', 'status' => 'vacant'],
            ['stall_code' => 'WM-002',        'section' => 'Wet Market',    'type' => 'inside',   'sqm' => 6.00,  'floor_level' => 'Ground Floor', 'status' => 'vacant'],
            ['stall_code' => 'LS-001',        'section' => 'Livestock',     'type' => 'inside',   'sqm' => 10.00, 'floor_level' => 'Ground Floor', 'status' => 'vacant'],
            ['stall_code' => 'COM-001',       'section' => 'Commercial',    'type' => 'inside',   'sqm' => 12.00, 'floor_level' => 'Ground Floor', 'status' => 'vacant'],
            ['stall_code' => 'EXT-ROW-A-001', 'section' => 'Outside Row A', 'type' => 'outside',  'sqm' => null,  'floor_level' => null,           'status' => 'vacant'],
            ['stall_code' => 'EXT-ROW-A-002', 'section' => 'Outside Row A', 'type' => 'outside',  'sqm' => null,  'floor_level' => null,           'status' => 'vacant'],
            ['stall_code' => 'EXT-ROW-A-003', 'section' => 'Outside Row A', 'type' => 'outside',  'sqm' => null,  'floor_level' => null,           'status' => 'vacant'],
            ['stall_code' => 'EXT-ROW-B-001', 'section' => 'Outside Row B', 'type' => 'outside',  'sqm' => null,  'floor_level' => null,           'status' => 'vacant'],
            ['stall_code' => 'EXT-ROW-B-002', 'section' => 'Outside Row B', 'type' => 'outside',  'sqm' => null,  'floor_level' => null,           'status' => 'vacant'],
            ['stall_code' => 'AMBU',          'section' => 'Ambulant',      'type' => 'ambulant', 'sqm' => null,  'floor_level' => null,           'status' => 'vacant'],
        ]);

        // --- vendors ---
        $year = date('Y');
        $this->db->table('vendors')->insertBatch([
            ['vendor_no' => "VND-{$year}-0001", 'first_name' => 'Rosa',  'last_name' => 'Dela Cruz', 'business_name' => 'Rosa Dry Goods',     'contact' => '09171234567', 'type' => 'inside',   'status' => 'active'],
            ['vendor_no' => "VND-{$year}-0002", 'first_name' => 'Pedro', 'last_name' => 'Santos',    'business_name' => 'Santos Wet Market',  'contact' => '09181234567', 'type' => 'inside',   'status' => 'active'],
            ['vendor_no' => "VND-{$year}-0003", 'first_name' => 'Maria', 'last_name' => 'Reyes',     'business_name' => null,                 'contact' => '09191234567', 'type' => 'outside',  'status' => 'active'],
            ['vendor_no' => "VND-{$year}-0004", 'first_name' => 'Jose',  'last_name' => 'Garcia',    'business_name' => null,                 'contact' => '09201234567', 'type' => 'ambulant', 'status' => 'active'],
        ]);

        // --- vendor_stalls ---
        $this->db->table('vendor_stalls')->insertBatch([
            [
                'vendor_id'     => 1,
                'stall_id'      => 1,
                'permit_no'     => 'PRM-' . $year . '-001',
                'permit_issued' => date('Y-m-d', strtotime('-6 months')),
                'permit_expiry' => date('Y-m-d', strtotime('+20 days')),
                'assigned_date' => date('Y-m-d', strtotime('-6 months')),
                'status'        => 'active',
            ],
            [
                'vendor_id'     => 2,
                'stall_id'      => 4,
                'permit_no'     => 'PRM-' . $year . '-002',
                'permit_issued' => date('Y-m-d', strtotime('-3 months')),
                'permit_expiry' => date('Y-m-d', strtotime('+60 days')),
                'assigned_date' => date('Y-m-d', strtotime('-3 months')),
                'status'        => 'active',
            ],
            [
                'vendor_id'     => 3,
                'stall_id'      => 8,
                'permit_no'     => 'PRM-' . $year . '-003',
                'permit_issued' => date('Y-m-d', strtotime('-1 month')),
                'permit_expiry' => date('Y-m-d', strtotime('+90 days')),
                'assigned_date' => date('Y-m-d', strtotime('-1 month')),
                'status'        => 'active',
            ],
        ]);

        $this->db->table('stalls')->whereIn('id', [1, 4, 8])->update(['status' => 'occupied']);

        // --- payments ---
        $monthStart = date('Y-m-01');
        $monthEnd   = date('Y-m-t');
        $weekStart  = date('Y-m-d', strtotime('monday this week'));
        $weekEnd    = date('Y-m-d', strtotime($weekStart . ' +6 days'));

        $refPrefix = 'ARK-' . date('Ymd') . '-';

        $this->db->table('payments')->insertBatch([
            [
                'reference_no'    => $refPrefix . '0001',
                'vendor_id'       => 1,
                'stall_id'        => 1,
                'rate_id'         => $rateId,
                'payment_type'    => 'monthly',
                'sqm_charged'     => 6.00,
                'rate_used'       => 45.00,
                'computed_amount' => 270.00,
                'amount_paid'     => 270.00,
                'period_start'    => $monthStart,
                'period_end'      => $monthEnd,
                'collected_by'    => $collector1Id,
                'payment_date'    => new RawSql('CURRENT_TIMESTAMP'),
                'notes'           => 'Sample monthly payment',
            ],
            [
                'reference_no'    => $refPrefix . '0002',
                'vendor_id'       => 2,
                'stall_id'        => 4,
                'rate_id'         => $rateId,
                'payment_type'    => 'monthly',
                'sqm_charged'     => 8.00,
                'rate_used'       => 45.00,
                'computed_amount' => 360.00,
                'amount_paid'     => 360.00,
                'period_start'    => $monthStart,
                'period_end'      => $monthEnd,
                'collected_by'    => $collector1Id,
                'payment_date'    => new RawSql('CURRENT_TIMESTAMP'),
                'notes'           => null,
            ],
            [
                'reference_no'    => $refPrefix . '0003',
                'vendor_id'       => 3,
                'stall_id'        => 8,
                'rate_id'         => $rateId,
                'payment_type'    => 'weekly',
                'sqm_charged'     => null,
                'rate_used'       => 150.00,
                'computed_amount' => 150.00,
                'amount_paid'     => 150.00,
                'period_start'    => $weekStart,
                'period_end'      => $weekEnd,
                'collected_by'    => $collector2Id,
                'payment_date'    => new RawSql('CURRENT_TIMESTAMP'),
                'notes'           => null,
            ],
        ]);
    }
}

