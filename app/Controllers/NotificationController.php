<?php

namespace App\Controllers;

use App\Models\StallModel;
use App\Models\VendorModel;

class NotificationController extends BaseController
{
    public function index(): string|object
    {
        $role        = session()->get('user_role');
        $vendorModel = new VendorModel();
        $stallModel  = new StallModel();

        $overdue  = [];
        $permits  = [];
        $vacant   = [];
        $vacantCount = 0;

        if (in_array($role, ['admin', 'supervisor', 'staff'], true)) {
            $overdue     = $vendorModel->getOverdue();
            $permits     = $vendorModel->getExpiringPermits(30);
            $vacant      = $stallModel->getVacantReport();
            $vacantCount = $stallModel->countVacant();
        } elseif ($role === 'collector') {
            // Collectors see permit reminders only (collection-focused)
            $permits = $vendorModel->getExpiringPermits(30);
        }

        return view('notifications/index', $this->viewData([
            'page_title'       => 'Notifications & Alerts',
            'overdue'          => $overdue,
            'expiring_permits' => $permits,
            'vacant_stalls'    => $vacant,
            'vacant_count'     => $vacantCount,
            'show_overdue'     => in_array($role, ['admin', 'supervisor', 'staff'], true),
            'show_vacant'      => in_array($role, ['admin', 'supervisor', 'staff'], true),
        ]));
    }
}
