<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // -------------------------------------------------------------------------
    // USER MANAGEMENT INTERFACE (ADMIN ONLY)
    // -------------------------------------------------------------------------

    protected function checkAdmin()
    {
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'You do not have permission to access that page.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $data = [
            'page_title' => 'Manage Users',
            'user_name'  => session()->get('user_name'),
            'user_role'  => session()->get('user_role'),
            'users'      => $this->userModel->findAll()
        ];

        return view('users/index', $data);
    }

    public function create()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $data = [
            'page_title' => 'Create User',
            'user_name'  => session()->get('user_name'),
            'user_role'  => session()->get('user_role')
        ];

        return view('users/create', $data);
    }

    public function store()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        if (! $this->validate([
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'role'     => 'required|in_list[admin,staff]'
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->userModel->insert([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'     => $this->request->getPost('role')
        ]);

        return redirect()->to('/users')->with('message', 'User created successfully.');
    }

    public function edit($id)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        $data = [
            'page_title' => 'Edit User',
            'user_name'  => session()->get('user_name'),
            'user_role'  => session()->get('user_role'),
            'user'       => $user
        ];

        return view('users/edit', $data);
    }

    public function update($id)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        $rules = [
            'name'  => 'required|min_length[3]|max_length[100]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role'  => 'required|in_list[admin,staff]'
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[8]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role'  => $this->request->getPost('role')
        ];

        if ($password = $this->request->getPost('password')) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->userModel->update($id, $updateData);

        return redirect()->to('/users')->with('message', 'User updated successfully.');
    }

    public function delete($id)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $this->userModel->delete($id);
        return redirect()->to('/users')->with('message', 'User deleted successfully.');
    }

    // -------------------------------------------------------------------------
    // PROFILE MANAGEMENT
    // -------------------------------------------------------------------------

    public function profile()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        $data = [
            'page_title' => 'My Profile',
            'user_name'  => session()->get('user_name'),
            'user_role'  => session()->get('user_role'),
            'user'       => $user
        ];

        return view('users/profile', $data);
    }

    public function updateProfile()
    {
        $userId = session()->get('user_id');

        $rules = [
            'name'  => 'required|min_length[3]|max_length[100]',
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]"
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[8]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email')
        ];

        if ($password = $this->request->getPost('password')) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->userModel->update($userId, $updateData);

        // Update session user_name if it changed
        session()->set('user_name', $updateData['name']);

        return redirect()->to('/profile')->with('message', 'Profile updated successfully.');
    }
}
