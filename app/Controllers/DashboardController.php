<?php

namespace App\Controllers;

use App\Models\VendorModel;
use App\Models\PaymentModel;

class DashboardController extends BaseController
{
    public function index(): string|object
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $thisMonthStart = date('Y-m-01') . ' 00:00:00';
        $thisMonthEnd   = date('Y-m-t') . ' 23:59:59';

        // 1. Total Vendors
        $vendorModel = new VendorModel();
        $totalVendors = $vendorModel->countAllResults();

        // 2. Active Stalls (Active Vendors)
        $activeStalls = $vendorModel->where('status', 'active')->countAllResults();

        // 3. Total Collections This Month
        $monthCollections = $db->table('payments')
            ->where('created_at >=', $thisMonthStart)
            ->where('created_at <=', $thisMonthEnd)
            ->selectSum('amount')
            ->get()
            ->getRowArray();
        $totalCollectionsThisMonth = (float) ($monthCollections['amount'] ?? 0.00);

        // 4. Overdue Accounts Count
        // Active vendors that have no payment or whose maximum period_end is in the past
        $overdueCount = $db->table('vendors')
            ->where('status', 'active')
            ->where("id NOT IN (
                SELECT vendor_id FROM payments 
                GROUP BY vendor_id 
                HAVING MAX(period_end) >= '{$today}'
            )")
            ->countAllResults();

        // Bar chart data for the past 6 months
        $chartLabels = [];
        $chartData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $mStart = date('Y-m-01', strtotime("-{$i} months"));
            $mEnd   = date('Y-m-t', strtotime("-{$i} months"));
            $mLabel = date('M Y', strtotime("-{$i} months"));

            $sum = $db->table('payments')
                ->where('created_at >=', $mStart . ' 00:00:00')
                ->where('created_at <=', $mEnd . ' 23:59:59')
                ->selectSum('amount')
                ->get()
                ->getRowArray();

            $chartLabels[] = $mLabel;
            $chartData[]   = (float) ($sum['amount'] ?? 0.00);
        }

        // Recent Payments (Last 10 transactions)
        $recentPayments = $db->table('payments')
            ->select('payments.*, vendors.name as vendor_name, vendors.stall_number, users.name as collector_name')
            ->join('vendors', 'vendors.id = payments.vendor_id')
            ->join('users', 'users.id = payments.collected_by', 'left')
            ->orderBy('payments.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $overdueVendors = $vendorModel->getOverdueVendors();
        $expiringPermits = $vendorModel->getExpiringPermits();

        return view('dashboard/index', [
            'page_title'                  => 'Dashboard',
            'user_name'                   => session()->get('user_name'),
            'user_role'                   => session()->get('user_role'),
            'total_vendors'               => $totalVendors,
            'active_stalls'               => $activeStalls,
            'total_collections_this_month'=> $totalCollectionsThisMonth,
            'overdue_accounts_count'      => $overdueCount,
            'chart_labels'                => json_encode($chartLabels),
            'chart_data'                  => json_encode($chartData),
            'recent_payments'             => $recentPayments,
            'overdue_vendors'             => $overdueVendors,
            'expiring_permits'            => $expiringPermits
        ]);
    }

    public function notifications(): string|object
    {
        $vendorModel = new VendorModel();
        $overdueVendors = $vendorModel->getOverdueVendors();
        $expiringPermits = $vendorModel->getExpiringPermits();

        return view('dashboard/notifications', [
            'page_title'       => 'System Notifications',
            'user_name'        => session()->get('user_name'),
            'user_role'        => session()->get('user_role'),
            'overdue_vendors'  => $overdueVendors,
            'expiring_permits' => $expiringPermits
        ]);
    }
}
