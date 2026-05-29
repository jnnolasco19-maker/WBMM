<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    private function checkAdminOnly()
    {
        if (session()->get('user_role') !== 'admin') {
            return true;
        }
        return false;
    }

    public function index(): string|object
    {
        if ($this->checkAdminOnly()) {
            return redirect()->to('/dashboard')->with('error', 'Access Denied. Admin privileges required.');
        }

        $userModel = new UserModel();
        $users = $userModel->orderBy('created_at', 'DESC')->findAll();

        return view('users/index', [
            'page_title' => 'User Management',
            'user_name'  => session()->get('user_name'),
            'user_role'  => session()->get('user_role'),
            'users'      => $users
        ]);
    }

    public function create(): string|object
    {
        if ($this->checkAdminOnly()) {
            return redirect()->to('/dashboard')->with('error', 'Access Denied. Admin privileges required.');
        }

        return view('users/create', [
            'page_title' => 'Add User',
            'user_name'  => session()->get('user_name'),
            'user_role'  => session()->get('user_role')
        ]);
    }

    public function store(): object
    {
        if ($this->checkAdminOnly()) {
            return redirect()->to('/dashboard')->with('error', 'Access Denied. Admin privileges required.');
        }

        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]|max_length[100]',
            'role'     => 'required|in_list[admin,staff]',
            'status'   => 'required|in_list[active,inactive]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $userModel->insert([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'     => $this->request->getPost('role'),
            'status'   => $this->request->getPost('status')
        ]);

        $newId = $userModel->getInsertID();
        $this->logActivity('Created user account for: ' . $this->request->getPost('email') . ' as ' . $this->request->getPost('role'), 'users', $newId);

        return redirect()->to('/users')
            ->with('message', 'User account registered successfully.');
    }

    public function edit(int $id): string|object
    {
        if ($this->checkAdminOnly()) {
            return redirect()->to('/dashboard')->with('error', 'Access Denied. Admin privileges required.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (! $user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        return view('users/edit', [
            'page_title' => 'Edit User Profile',
            'user_name'  => session()->get('user_name'),
            'user_role'  => session()->get('user_role'),
            'user'       => $user
        ]);
    }

    public function update(int $id): object
    {
        if ($this->checkAdminOnly()) {
            return redirect()->to('/dashboard')->with('error', 'Access Denied. Admin privileges required.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (! $user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email,id,' . $id . ']',
            'role'     => 'required|in_list[admin,staff]',
            'status'   => 'required|in_list[active,inactive]'
        ];

        // Only require password if they are changing it
        if ($this->request->getPost('password') !== '') {
            $rules['password'] = 'required|min_length[6]|max_length[100]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $status = $this->request->getPost('status');
        $role   = $this->request->getPost('role');

        // Self modification security checks
        if ($id === (int) session()->get('user_id')) {
            if ($status !== 'active') {
                return redirect()->back()->withInput()
                    ->with('error', 'You cannot deactivate your own account.');
            }
            if ($role !== 'admin') {
                return redirect()->back()->withInput()
                    ->with('error', 'You cannot demote your own account role from admin.');
            }
        }

        $data = [
            'name'   => $this->request->getPost('name'),
            'email'  => $this->request->getPost('email'),
            'role'   => $role,
            'status' => $status
        ];

        if ($this->request->getPost('password') !== '') {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        }

        $userModel->update($id, $data);
        $this->logActivity('Updated user account details for: ' . $this->request->getPost('email'), 'users', $id);

        return redirect()->to('/users')
            ->with('message', 'User account updated successfully.');
    }
}
