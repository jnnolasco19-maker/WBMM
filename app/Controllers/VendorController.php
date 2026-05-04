<?php

namespace App\Controllers;

use App\Models\VendorModel;
use App\Models\StallModel;

class VendorController extends BaseController
{
    private function getRole(): string
    {
        return (string) session()->get('user_role');
    }

    private function requireAdmin(): ?object
    {
        $role = $this->getRole();
        if ($role === 'admin') {
            return null;
        }
        if (! in_array($role, ['admin', 'staff'], true)) {
            session()->destroy();
            return redirect()->to(base_url('login'));
        }
        return $this->response->setStatusCode(403)->setBody(view('errors/403', [
            'user_name' => session()->get('user_name'),
            'user_role' => $role,
        ]));
    }

    // -------------------------------------------------------------------------
    // LIST
    // -------------------------------------------------------------------------

    public function index(): string|object
    {
        $role = $this->getRole();
        if (! in_array($role, ['admin', 'staff'], true)) {
            session()->destroy();
            return redirect()->to(base_url('login'));
        }

        $model  = new VendorModel();
        $search = (string) $this->request->getGet('search');
        $status = (string) $this->request->getGet('status');

        $vendors = $model->getFiltered($search, $status)->paginate(15, 'vendors');

        return view('vendors/index', [
            'page_title' => 'Vendors',
            'user_name'  => session()->get('user_name'),
            'user_role'  => $role,
            'vendors'    => $vendors,
            'pager'      => $model->pager,
            'search'     => $search,
            'status'     => $status,
        ]);
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    public function create(): string|object
    {
        $guard = $this->requireAdmin();
        if ($guard !== null) return $guard;

        return view('vendors/create', [
            'page_title' => 'Add Vendor',
            'user_name'  => session()->get('user_name'),
            'user_role'  => $this->getRole(),
            'validation' => null,
        ]);
    }

    public function store(): object
    {
        $guard = $this->requireAdmin();
        if ($guard !== null) return $guard;

        $rules = [
            'name'           => 'required|max_length[150]',
            'email'          => 'permit_empty|valid_email|max_length[150]',
            'contact_number' => 'permit_empty|max_length[20]',
            'status'         => 'required|in_list[active,inactive]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = trim((string) $this->request->getPost('email'));

        // Unique email check
        if ($email !== '') {
            $model = new VendorModel();
            if ($model->where('email', $email)->first()) {
                return redirect()->back()->withInput()
                    ->with('errors', ['email' => 'This email address is already in use.']);
            }
        }

        $model = new VendorModel();
        $model->insert([
            'name'           => $this->request->getPost('name'),
            'contact_number' => $this->request->getPost('contact_number'),
            'email'          => $email ?: null,
            'address'        => $this->request->getPost('address'),
            'status'         => $this->request->getPost('status'),
        ]);

        return redirect()->to(base_url('vendors'))
            ->with('message', 'Vendor created successfully.');
    }

    // -------------------------------------------------------------------------
    // EDIT / UPDATE
    // -------------------------------------------------------------------------

    public function edit(int $id): string|object
    {
        $guard = $this->requireAdmin();
        if ($guard !== null) return $guard;

        $model  = new VendorModel();
        $vendor = $model->find($id);

        if (! $vendor) {
            return redirect()->to(base_url('vendors'))
                ->with('error', 'Vendor not found.');
        }

        return view('vendors/edit', [
            'page_title' => 'Edit Vendor',
            'user_name'  => session()->get('user_name'),
            'user_role'  => $this->getRole(),
            'vendor'     => $vendor,
        ]);
    }

    public function update(int $id): object
    {
        $guard = $this->requireAdmin();
        if ($guard !== null) return $guard;

        $model  = new VendorModel();
        $vendor = $model->find($id);

        if (! $vendor) {
            return redirect()->to(base_url('vendors'))
                ->with('error', 'Vendor not found.');
        }

        $rules = [
            'name'           => 'required|max_length[150]',
            'email'          => 'permit_empty|valid_email|max_length[150]',
            'contact_number' => 'permit_empty|max_length[20]',
            'status'         => 'required|in_list[active,inactive]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = trim((string) $this->request->getPost('email'));

        // Unique email check — exclude current vendor
        if ($email !== '') {
            $duplicate = $model->where('email', $email)->where('id !=', $id)->first();
            if ($duplicate) {
                return redirect()->back()->withInput()
                    ->with('errors', ['email' => 'This email address is already in use.']);
            }
        }

        $model->update($id, [
            'name'           => $this->request->getPost('name'),
            'contact_number' => $this->request->getPost('contact_number'),
            'email'          => $email ?: null,
            'address'        => $this->request->getPost('address'),
            'status'         => $this->request->getPost('status'),
        ]);

        return redirect()->to(base_url('vendors'))
            ->with('message', 'Vendor updated successfully.');
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    public function delete(int $id): object
    {
        $guard = $this->requireAdmin();
        if ($guard !== null) return $guard;

        $model  = new VendorModel();
        $vendor = $model->find($id);

        if (! $vendor) {
            return redirect()->to(base_url('vendors'))
                ->with('error', 'Vendor not found.');
        }

        // Vacate stalls assigned to this vendor before deleting
        $stallModel = new StallModel();
        $stallModel->vacateByVendor($id);

        $model->delete($id);

        return redirect()->to(base_url('vendors'))
            ->with('message', 'Vendor deleted successfully.');
    }
}
