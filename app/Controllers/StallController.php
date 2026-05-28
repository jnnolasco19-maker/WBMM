<?php

namespace App\Controllers;

use App\Models\StallModel;
use App\Models\VendorModel;

class StallController extends BaseController
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

        $model  = new StallModel();
        $search = (string) $this->request->getGet('search');
        $status = (string) $this->request->getGet('status');

        $stalls = $model->getFiltered($search, $status)->paginate(15, 'stalls');

        return view('stalls/index', [
            'page_title' => 'Stalls',
            'user_name'  => session()->get('user_name'),
            'user_role'  => $role,
            'stalls'     => $stalls,
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

        $vendorModel = new VendorModel();
        $vendors     = $vendorModel->where('status', 'active')->findAll();

        return view('stalls/create', [
            'page_title' => 'Add Stall',
            'user_name'  => session()->get('user_name'),
            'user_role'  => $this->getRole(),
            'vendors'    => $vendors,
        ]);
    }

    public function store(): object
    {
        $guard = $this->requireAdmin();
        if ($guard !== null) return $guard;

        $rules = [
            'stall_number' => 'required|max_length[20]',
            'location'     => 'permit_empty|max_length[150]',
            'size'         => 'permit_empty|max_length[50]',
            'status'       => 'required|in_list[occupied,vacant]',
            'vendor_id'    => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $stallNumber = trim((string) $this->request->getPost('stall_number'));
        $model       = new StallModel();

        // Unique stall_number check
        if ($model->where('stall_number', $stallNumber)->first()) {
            return redirect()->back()->withInput()
                ->with('errors', ['stall_number' => 'This stall number is already in use.']);
        }

        $vendorId = $this->request->getPost('vendor_id');

        // Validate vendor_id references existing vendor
        if ($vendorId) {
            $vendorModel = new VendorModel();
            if (! $vendorModel->find((int) $vendorId)) {
                return redirect()->back()->withInput()
                    ->with('errors', ['vendor_id' => 'The selected vendor does not exist.']);
            }
        }

        $model->insert([
            'stall_number' => $stallNumber,
            'location'     => $this->request->getPost('location'),
            'size'         => $this->request->getPost('size'),
            'status'       => $this->request->getPost('status'),
            'vendor_id'    => $vendorId ?: null,
        ]);

        return redirect()->to(base_url('stalls'))
            ->with('message', 'Stall created successfully.');
    }

    // -------------------------------------------------------------------------
    // EDIT / UPDATE
    // -------------------------------------------------------------------------

    public function edit(int $id): string|object
    {
        $guard = $this->requireAdmin();
        if ($guard !== null) return $guard;

        $model = new StallModel();
        $stall = $model->find($id);

        if (! $stall) {
            return redirect()->to(base_url('stalls'))
                ->with('error', 'Stall not found.');
        }

        $vendorModel = new VendorModel();
        $vendors     = $vendorModel->where('status', 'active')->findAll();

        return view('stalls/edit', [
            'page_title' => 'Edit Stall',
            'user_name'  => session()->get('user_name'),
            'user_role'  => $this->getRole(),
            'stall'      => $stall,
            'vendors'    => $vendors,
        ]);
    }

    public function update(int $id): object
    {
        $guard = $this->requireAdmin();
        if ($guard !== null) return $guard;

        $model = new StallModel();
        $stall = $model->find($id);

        if (! $stall) {
            return redirect()->to(base_url('stalls'))
                ->with('error', 'Stall not found.');
        }

        $rules = [
            'stall_number' => 'required|max_length[20]',
            'location'     => 'permit_empty|max_length[150]',
            'size'         => 'permit_empty|max_length[50]',
            'status'       => 'required|in_list[occupied,vacant]',
            'vendor_id'    => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $stallNumber = trim((string) $this->request->getPost('stall_number'));

        // Unique stall_number check — exclude current stall
        $duplicate = $model->where('stall_number', $stallNumber)->where('id !=', $id)->first();
        if ($duplicate) {
            return redirect()->back()->withInput()
                ->with('errors', ['stall_number' => 'This stall number is already in use.']);
        }

        $vendorId = $this->request->getPost('vendor_id');

        if ($vendorId) {
            $vendorModel = new VendorModel();
            if (! $vendorModel->find((int) $vendorId)) {
                return redirect()->back()->withInput()
                    ->with('errors', ['vendor_id' => 'The selected vendor does not exist.']);
            }
        }

        $model->update($id, [
            'stall_number' => $stallNumber,
            'location'     => $this->request->getPost('location'),
            'size'         => $this->request->getPost('size'),
            'status'       => $this->request->getPost('status'),
            'vendor_id'    => $vendorId ?: null,
        ]);

        return redirect()->to(base_url('stalls'))
            ->with('message', 'Stall updated successfully.');
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    public function delete(int $id): object
    {
        $guard = $this->requireAdmin();
        if ($guard !== null) return $guard;

        $model = new StallModel();
        $stall = $model->find($id);

        if (! $stall) {
            return redirect()->to(base_url('stalls'))
                ->with('error', 'Stall not found.');
        }

        $model->delete($id);

        return redirect()->to(base_url('stalls'))
            ->with('message', 'Stall deleted successfully.');
    }
}
