<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\StallModel;
use App\Models\VendorModel;

class DashboardController extends BaseController
{
    public function index(): string|object
    {
        $role = session()->get('user_role');

        if ($role === 'collector') {
            return redirect()->to('/payments/create');
        }

        if (! in_array($role, ['admin', 'supervisor', 'staff'], true)) {
            return redirect()->to('/login')->with('error', 'Access denied.');
        }

        $vendorModel = new VendorModel();
        $stallModel  = new StallModel();
        $payModel    = new PaymentModel();

        $overdueVendors = $vendorModel->getOverdue();
        $monthlyData    = $payModel->getMonthlyTotals();

        $chartLabels = [];
        $chartInside = [];
        $chartOutside = [];
        $chartAmbulant = [];

        for ($i = 5; $i >= 0; $i--) {
            $key           = date('Y-m', strtotime("-{$i} months"));
            $chartLabels[] = date('M Y', strtotime("-{$i} months"));
            $chartInside[] = $monthlyData[$key]['inside'] ?? 0;
            $chartOutside[] = $monthlyData[$key]['outside'] ?? 0;
            $chartAmbulant[] = $monthlyData[$key]['ambulant'] ?? 0;
        }

        return view('dashboard/index', $this->viewData([
            'page_title'             => 'Dashboard',
            'total_active_vendors'   => $vendorModel->where('status', 'active')->countAllResults(),
            'occupied_inside'        => $stallModel->countOccupiedInside(),
            'occupied_outside'       => $stallModel->countOccupiedOutside(),
            'total_vacant'           => $stallModel->countVacant(),
            'collections_this_month' => $payModel->getTotalThisMonth(),
            'overdue_count'          => count($overdueVendors),
            'overdue_vendors'        => array_slice($overdueVendors, 0, 5),
            'expiring_permits'       => $vendorModel->getExpiringPermits(30),
            'recent_payments'        => $payModel->getRecent(10),
            'chart_labels'           => json_encode($chartLabels),
            'chart_inside'           => json_encode($chartInside),
            'chart_outside'          => json_encode($chartOutside),
            'chart_ambulant'         => json_encode($chartAmbulant),
        ]));
    }
}
