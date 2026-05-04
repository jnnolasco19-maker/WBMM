<?php

namespace App\Controllers;

use App\Models\DashboardModel;

class DashboardController extends BaseController
{
    public function index(): string|object
    {
        $session  = session();
        $userName = $session->get('user_name');
        $userRole = $session->get('user_role');

        // Guard: unknown role — destroy session and redirect
        if (! in_array($userRole, ['admin', 'staff'], true)) {
            $session->destroy();
            return redirect()->to('/login');
        }

        $model = new DashboardModel();

        $stats = match ($userRole) {
            'admin' => $model->getAdminStats(),
            'staff' => $model->getStaffStats(),
        };

        $data = [
            'page_title' => 'Dashboard',
            'user_name'  => $userName,
            'user_role'  => $userRole,
            'stats'      => $stats,
            'loaded_at'  => date('Y-m-d H:i:s'),
        ];

        return view('dashboard/index', $data);
    }
}
