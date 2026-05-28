<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (! session()->get('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Authentication required.');
        }

        // If no role arguments, just check login
        if (empty($arguments)) {
            return;
        }

        $userRole = session()->get('user_role');
        $userRoleLevel = session()->get('user_role_level');

        // Check if user has any of the required roles
        $hasAccess = false;
        
        foreach ($arguments as $requiredRole) {
            // Direct role match
            if (strtolower($userRole) === strtolower($requiredRole)) {
                $hasAccess = true;
                break;
            }
            
            // Admin has access to everything
            if (strtolower($userRole) === 'admin') {
                $hasAccess = true;
                break;
            }
        }

        if (! $hasAccess) {
            return redirect()->to('/dashboard')
                ->with('error', 'Access Denied. You do not have permission to access this page.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed here
    }
}
