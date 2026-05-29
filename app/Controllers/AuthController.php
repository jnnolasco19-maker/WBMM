<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login(): string|object
    {
        if (session()->get('is_logged_in')) {
            return $this->redirectByRole();
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->processLogin();
        }

        return view('auth/login', ['page_title' => 'Sign In']);
    }

    public function logout(): object
    {
        if (session()->get('is_logged_in')) {
            (new AuditLogModel())->log('logout', 'users', (int) session()->get('user_id'), 'User logged out');
        }
        session()->destroy();

        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }

    private function processLogin(): object
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Invalid email or password.');
        }

        $user = (new UserModel())->findByEmail($this->request->getPost('email'));

        if (! $user || ! password_verify($this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->withInput()
                ->with('error', 'Invalid email or password.');
        }

        if ($user['status'] !== 'active') {
            return redirect()->back()->withInput()
                ->with('error', 'Your account has been deactivated. Contact the administrator.');
        }

        session()->set([
            'user_id'      => (int) $user['id'],
            'user_name'    => $user['name'],
            'user_role'    => $user['role'],
            'is_logged_in' => true,
        ]);

        (new AuditLogModel())->log('login', 'users', (int) $user['id'], 'User logged in');

        return $this->redirectByRole();
    }

    private function redirectByRole(): object
    {
        if (session()->get('user_role') === 'collector') {
            return redirect()->to('/payments/create');
        }

        return redirect()->to('/dashboard');
    }
}
