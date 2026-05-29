<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    public function index(): string|object
    {
        $guard = $this->requireRoles(['admin']);
        if ($guard !== true) {
            return $guard;
        }

        return view('users/index', $this->viewData([
            'page_title' => 'User Management',
            'users'      => (new UserModel())->getAllUsers(),
        ]));
    }

    public function create(): string|object
    {
        $guard = $this->requireRoles(['admin']);
        if ($guard !== true) {
            return $guard;
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->saveUser();
        }

        return view('users/create', $this->viewData(['page_title' => 'Add User']));
    }

    public function edit(int $id): string|object
    {
        $guard = $this->requireRoles(['admin']);
        if ($guard !== true) {
            return $guard;
        }

        $user = (new UserModel())->find($id);
        if (! $user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->saveUser($id, $user);
        }

        return view('users/edit', $this->viewData([
            'page_title' => 'Edit User',
            'user'       => $user,
        ]));
    }

    private function saveUser(?int $id = null, ?array $existing = null): object
    {
        $isEdit = $id !== null;

        $rules = [
            'name'   => 'required|max_length[100]',
            'email'  => $isEdit
                ? "required|valid_email|is_unique[users.email,id,{$id}]"
                : 'required|valid_email|is_unique[users.email]',
            'role'   => 'required|in_list[admin,supervisor,collector,staff]',
            'status' => 'required|in_list[active,inactive]',
        ];

        if (! $isEdit) {
            $rules['password'] = 'required|min_length[8]';
        } elseif ($this->request->getPost('password') !== '') {
            $rules['password'] = 'min_length[8]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($isEdit && $id === (int) session()->get('user_id')) {
            if ($this->request->getPost('status') !== 'active') {
                return redirect()->back()->withInput()->with('error', 'You cannot deactivate your own account.');
            }
        }

        $data = [
            'name'   => $this->request->getPost('name'),
            'email'  => $this->request->getPost('email'),
            'role'   => $this->request->getPost('role'),
            'status' => $this->request->getPost('status'),
        ];

        if ($this->request->getPost('password') !== '') {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        }

        $userModel = new UserModel();

        if ($isEdit) {
            $userModel->update($id, $data);
            $this->audit('update', 'users', $id, 'Updated user: ' . $data['email']);
            return redirect()->to('/users')->with('success', 'User updated successfully.');
        }

        $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        $userModel->insert($data);
        $newId = $userModel->getInsertID();
        $this->audit('create', 'users', $newId, 'Created user: ' . $data['email']);

        return redirect()->to('/users')->with('success', 'User created successfully.');
    }

    public function deactivate(int $id): object
    {
        $guard = $this->requireRoles(['admin']);
        if ($guard !== true) {
            return $guard;
        }

        if ($id === (int) session()->get('user_id')) {
            return redirect()->to('/users')->with('error', 'You cannot deactivate your own account.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (! $user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        $userModel->update($id, ['status' => 'inactive']);
        $this->audit('deactivate', 'users', $id, 'Deactivated user: ' . $user['email']);

        return redirect()->to('/users')->with('success', 'User deactivated successfully.');
    }
}
