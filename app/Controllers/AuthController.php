<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PasswordResetModel;

class AuthController extends BaseController
{
    // -------------------------------------------------------------------------
    // LOGIN
    // -------------------------------------------------------------------------

    public function loginForm(): string|object
    {
        // Already logged in — go to dashboard
        if (session()->get('is_logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function loginProcess(): object
    {
        $cache  = \Config\Services::cache();
        $ip     = $this->request->getIPAddress();
        $keyAtt = 'login_attempts_' . md5($ip);
        $keyBlk = 'login_blocked_' . md5($ip);

        // --- Rate limit check (IP-based, persists across browser tabs) ---
        $blockedUntil = $cache->get($keyBlk);
        if ($blockedUntil && time() < $blockedUntil) {
            $wait = ceil(($blockedUntil - time()) / 60);
            return redirect()->back()
                ->with('error', "Too many failed attempts. Please wait {$wait} minute(s) before trying again.");
        }

        // --- Input validation ---
        if (! $this->validate([
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[8]|max_length[72]',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // --- Credential check ---
        $userModel = new UserModel();
        $user      = $userModel->findByEmailWithPassword($email);

        if (! $user || ! password_verify($password, $user['password'])) {
            // Increment failed attempts (tracked by IP, not session)
            $attempts = (int) $cache->get($keyAtt) + 1;
            $cache->save($keyAtt, $attempts, 300); // store for 5 min

            if ($attempts >= 5) {
                $cache->save($keyBlk, time() + 300, 300);
                return redirect()->back()
                    ->with('error', 'Too many failed attempts. You are blocked for 5 minutes.');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'The email address or password you entered is incorrect.');
        }

        // --- Success: establish session ---
        session()->regenerate(true);
        session()->set([
            'user_id'      => $user['id'],
            'user_name'    => $user['name'],
            'user_role'    => $user['role'],
            'is_logged_in' => true,
        ]);

        // Clear IP-based rate-limit keys on successful login
        $cache  = \Config\Services::cache();
        $ip     = $this->request->getIPAddress();
        $cache->delete('login_attempts_' . md5($ip));
        $cache->delete('login_blocked_' . md5($ip));

        return redirect()->to('/dashboard');
    }

    // -------------------------------------------------------------------------
    // LOGOUT
    // -------------------------------------------------------------------------

    public function logout(): object
    {
        session()->destroy();
        return redirect()->to('/login')->with('message', 'You have been logged out successfully.');
    }

    public function logoutGet(): object
    {
        return $this->response->setStatusCode(405, 'Method Not Allowed');
    }

    // -------------------------------------------------------------------------
    // FORGOT PASSWORD
    // -------------------------------------------------------------------------

    public function forgotPasswordForm(): string
    {
        return view('auth/forgot_password');
    }

    public function forgotPasswordProcess(): object
    {
        if (! $this->validate(['email' => 'required|valid_email'])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email     = $this->request->getPost('email');
        $userModel = new UserModel();
        $user      = $userModel->findByEmail($email);

        // Always show the same message to prevent email enumeration
        $confirmation = 'If that email is registered, a reset link has been sent.';

        if ($user) {
            $token      = bin2hex(random_bytes(32));
            $expiresAt  = date('Y-m-d H:i:s', strtotime('+60 minutes'));

            $resetModel = new PasswordResetModel();
            $resetModel->createToken($email, $token, $expiresAt);

            // Send email with reset link
            $resetLink = base_url("reset-password/{$token}");
            $emailService = \Config\Services::email();
            $emailService->setTo($email);
            $emailService->setSubject('WBMM Password Reset');
            $emailService->setMessage("Click the link to reset your password: {$resetLink}");
            $emailService->send();
        }

        return redirect()->back()->with('message', $confirmation);
    }

    // -------------------------------------------------------------------------
    // RESET PASSWORD
    // -------------------------------------------------------------------------

    public function resetPasswordForm(string $token): string|object
    {
        $resetModel = new PasswordResetModel();
        $record     = $resetModel->findValidToken($token);

        if (! $record) {
            return redirect()->to('/forgot-password')
                ->with('error', 'This reset link is invalid or has expired.');
        }

        return view('auth/reset_password', ['token' => $token]);
    }

    public function resetPasswordProcess(string $token): object
    {
        $resetModel = new PasswordResetModel();
        $record     = $resetModel->findValidToken($token);

        if (! $record) {
            return redirect()->to('/forgot-password')
                ->with('error', 'This reset link is invalid or has expired.');
        }

        if (! $this->validate(['password' => 'required|min_length[8]'])) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors());
        }

        $newHash   = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        $userModel = new UserModel();
        $user      = $userModel->findByEmail($record['email']);

        if ($user) {
            $userModel->updatePassword($user['id'], $newHash);
            $resetModel->invalidateToken($token);
        }

        return redirect()->to('/login')
            ->with('message', 'Your password has been reset. Please log in.');
    }

    // -------------------------------------------------------------------------
    // REGISTER
    // -------------------------------------------------------------------------

    public function registerForm(): string|object
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/register');
    }

    public function registerProcess(): object
    {
        if (! $this->validate([
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]|max_length[72]',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $userModel->insert([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'     => 'staff', // Default role for public registration
        ]);

        return redirect()->to('/login')
            ->with('message', 'Registration successful. Please log in.');
    }
}
