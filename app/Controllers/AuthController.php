<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login(): string|object
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login', [
            'page_title' => 'Sign In'
        ]);
    }

    public function loginProcess(): object
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/dashboard');
        }

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]|max_length[100]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user      = $userModel->findByEmail($email);

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()
                ->with('error', 'Invalid email or password.');
        }

        if ($user['status'] !== 'active') {
            return redirect()->back()->withInput()
                ->with('error', 'Your account has been deactivated.');
        }

        // Establish session parameters
        session()->set([
            'user_id'      => (int) $user['id'],
            'user_name'    => $user['name'],
            'user_role'    => $user['role'],
            'is_logged_in' => true
        ]);

        $this->logActivity('User logged in successfully', 'users', $user['id']);

        return redirect()->to('/dashboard')
            ->with('message', 'Welcome back, ' . $user['name'] . '!');
    }

    public function logout(): object
    {
        if (session()->get('is_logged_in')) {
            $this->logActivity('User logged out', 'users', session()->get('user_id'));
        }
        
        session()->destroy();

        return redirect()->to('/login')
            ->with('message', 'You have been logged out successfully.');
    }
}