<?php

namespace App\Controllers;

use App\Models\VendorModel;

class VendorController extends BaseController
{
    private function checkAdminOnly()
    {
        if (session()->get('user_role') !== 'admin') {
            return true; // restricted
        }
        return false;
    }

    public function index(): string|object
    {
        $model        = new VendorModel();
        $search       = (string) $this->request->getGet('search');
        $section      = (string) $this->request->getGet('section');
        $status       = (string) $this->request->getGet('status');
        $expiringSoon = (bool) $this->request->getGet('expiring_soon');

        $vendors = $model->getFiltered($search, $section, $status, $expiringSoon)->paginate(15, 'vendors');

        return view('vendors/index', [
            'page_title'    => 'Vendors Directory',
            'user_name'     => session()->get('user_name'),
            'user_role'     => session()->get('user_role'),
            'vendors'       => $vendors,
            'pager'         => $model->pager,
            'search'        => $search,
            'section'       => $section,
            'status'        => $status,
            'expiring_soon' => $expiringSoon
        ]);
    }

    public function create(): string|object
    {
        return view('vendors/create', [
            'page_title' => 'Register Vendor',
            'user_name'  => session()->get('user_name'),
            'user_role'  => session()->get('user_role')
        ]);
    }

    public function store(): object
    {
        $rules = [
            'name'          => 'required|min_length[3]|max_length[100]',
            'stall_number'  => 'required|is_unique[vendors.stall_number]|max_length[50]',
            'section'       => 'required|in_list[Dry Goods,Wet Market,Livestock,Commercial]',
            'contact'       => 'permit_empty|max_length[20]',
            'permit_expiry' => 'required|valid_date[Y-m-d]',
            'status'        => 'required|in_list[active,inactive]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model = new VendorModel();
        $model->insert([
            'name'          => $this->request->getPost('name'),
            'stall_number'  => $this->request->getPost('stall_number'),
            'section'       => $this->request->getPost('section'),
            'contact'       => $this->request->getPost('contact') ?: null,
            'permit_expiry' => $this->request->getPost('permit_expiry'),
            'status'        => $this->request->getPost('status')
        ]);

        $vendorId = $model->getInsertID();
        $this->logActivity('Registered vendor: ' . $this->request->getPost('name'), 'vendors', $vendorId);

        return redirect()->to('/vendors')
            ->with('message', 'Vendor registered successfully.');
    }

    public function edit(int $id): string|object
    {
        if ($this->checkAdminOnly()) {
            return redirect()->to('/vendors')->with('error', 'Access Denied. Admin privileges required.');
        }

        $model  = new VendorModel();
        $vendor = $model->find($id);

        if (! $vendor) {
            return redirect()->to('/vendors')->with('error', 'Vendor not found.');
        }

        return view('vendors/edit', [
            'page_title' => 'Edit Vendor Profile',
            'user_name'  => session()->get('user_name'),
            'user_role'  => session()->get('user_role'),
            'vendor'     => $vendor
        ]);
    }

    public function update(int $id): object
    {
        if ($this->checkAdminOnly()) {
            return redirect()->to('/vendors')->with('error', 'Access Denied. Admin privileges required.');
        }

        $model  = new VendorModel();
        $vendor = $model->find($id);

        if (! $vendor) {
            return redirect()->to('/vendors')->with('error', 'Vendor not found.');
        }

        $rules = [
            'name'          => 'required|min_length[3]|max_length[100]',
            'stall_number'  => 'required|is_unique[vendors.stall_number,id,' . $id . ']|max_length[50]',
            'section'       => 'required|in_list[Dry Goods,Wet Market,Livestock,Commercial]',
            'contact'       => 'permit_empty|max_length[20]',
            'permit_expiry' => 'required|valid_date[Y-m-d]',
            'status'        => 'required|in_list[active,inactive]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model->update($id, [
            'name'          => $this->request->getPost('name'),
            'stall_number'  => $this->request->getPost('stall_number'),
            'section'       => $this->request->getPost('section'),
            'contact'       => $this->request->getPost('contact') ?: null,
            'permit_expiry' => $this->request->getPost('permit_expiry'),
            'status'        => $this->request->getPost('status')
        ]);

        $this->logActivity('Updated vendor profile for: ' . $this->request->getPost('name'), 'vendors', $id);

        return redirect()->to('/vendors')
            ->with('message', 'Vendor profile updated successfully.');
    }

    public function delete(int $id): object
    {
        if ($this->checkAdminOnly()) {
            return redirect()->to('/vendors')->with('error', 'Access Denied. Admin privileges required.');
        }

        $model  = new VendorModel();
        $vendor = $model->find($id);

        if (! $vendor) {
            return redirect()->to('/vendors')->with('error', 'Vendor not found.');
        }

        $model->delete($id);
        $this->logActivity('Deleted vendor: ' . $vendor['name'], 'vendors', $id);

        return redirect()->to('/vendors')
            ->with('message', 'Vendor deleted successfully.');
    }
}
