<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PermissionModel;
use App\Models\UserModel;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (! session()->get('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Authentication required.');
        }

        // If no permission arguments, just check login
        if (empty($arguments)) {
            return;
        }

        $userId = session()->get('user_id');
        $userRole = session()->get('user_role');
        
        // Admin has all permissions
        if (strtolower($userRole) === 'admin') {
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->findWithRole($userId);
        
        if (! $user || ! $user['role_id']) {
            return redirect()->to('/dashboard')
                ->with('error', 'Access Denied. No role assigned.');
        }

        $permissionModel = new PermissionModel();
        
        // Check if user has any of the required permissions
        $hasPermission = false;
        
        foreach ($arguments as $requiredPermission) {
            if ($permissionModel->roleHasPermission($user['role_id'], $requiredPermission)) {
                $hasPermission = true;
                break;
            }
        }

        if (! $hasPermission) {
            return redirect()->to('/dashboard')
                ->with('error', 'Access Denied. You do not have the required permissions.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed here
    }
}
