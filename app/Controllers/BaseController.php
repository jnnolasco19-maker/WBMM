<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\VendorModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper(['form', 'url']);
    }

    protected function viewData(array $extra = []): array
    {
        $role = session()->get('user_role');
        $alertCount = 0;

        if (in_array($role, ['admin', 'supervisor', 'staff'], true)) {
            $vendorModel = new VendorModel();
            $alertCount  = count($vendorModel->getOverdue())
                + count($vendorModel->getExpiringPermits(30));
        } elseif ($role === 'collector') {
            $alertCount = count((new VendorModel())->getExpiringPermits(30));
        }

        return array_merge([
            'user_name'   => session()->get('user_name'),
            'user_role'   => $role,
            'alert_count' => $alertCount,
        ], $extra);
    }

    protected function requireRoles(array $roles): bool|object
    {
        if (! in_array(session()->get('user_role'), $roles, true)) {
            return redirect()->to('/dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        return true;
    }

    protected function audit(string $action, string $table, ?int $recordId = null, string $details = ''): void
    {
        (new AuditLogModel())->log($action, $table, $recordId, $details);
    }
}
