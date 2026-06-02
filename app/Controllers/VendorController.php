<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\PaymentModel;
use App\Models\VendorModel;
use App\Models\VendorStallModel;

class VendorController extends BaseController
{
    private function requireAdmin(): bool|object
    {
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('/vendors')
                ->with('error', 'Only administrators can perform this action.');
        }

        return true;
    }

    private function canManageVendors(): bool
    {
        return in_array(session()->get('user_role'), ['admin', 'staff'], true);
    }

    public function index(): string
    {
        $model = new VendorModel();

        return view('vendors/index', $this->viewData([
            'page_title' => 'Vendors',
            'vendors'    => $model->getFiltered(
                (string) $this->request->getGet('search'),
                (string) $this->request->getGet('type'),
                (string) $this->request->getGet('status')
            ),
            'search'     => (string) $this->request->getGet('search'),
            'type'       => (string) $this->request->getGet('type'),
            'status'     => (string) $this->request->getGet('status'),
        ]));
    }

    public function create(): string|object
    {
        if (! $this->canManageVendors()) {
            return redirect()->to('/vendors')->with('error', 'Access denied.');
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->saveVendor();
        }

        $model = new VendorModel();

        return view('vendors/create', $this->viewData([
            'page_title'     => 'Register Vendor',
            'next_vendor_no' => $model->generateVendorNo(),
        ]));
    }

    public function edit(int $id): string|object
    {
        $guard = $this->requireAdmin();
        if ($guard !== true) {
            return $guard;
        }

        $vendor = (new VendorModel())->find($id);
        if (! $vendor) {
            return redirect()->to('/vendors')->with('error', 'Vendor not found.');
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->saveVendor($id);
        }

        return view('vendors/edit', $this->viewData([
            'page_title' => 'Edit Vendor',
            'vendor'     => $vendor,
        ]));
    }

    private function saveVendor(?int $id = null): object
    {
        $rules = [
            'first_name'             => 'required|max_length[100]',
            'last_name'              => 'required|max_length[100]',
            'type'                   => 'required|in_list[inside,outside,ambulant]',
            'status'                 => 'required|in_list[active,inactive,suspended]',
            'contact'                => 'permit_empty|max_length[20]',
            'barangay_permit_no'     => 'permit_empty|max_length[50]',
            'barangay_permit_issued' => 'permit_empty|valid_date[Y-m-d]',
            'barangay_permit_expiry' => 'permit_empty|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'first_name'             => $this->request->getPost('first_name'),
            'last_name'              => $this->request->getPost('last_name'),
            'business_name'          => $this->request->getPost('business_name') ?: null,
            'contact'                => $this->request->getPost('contact') ?: null,
            'address'                => $this->request->getPost('address') ?: null,
            'id_type'                => $this->request->getPost('id_type') ?: null,
            'id_number'              => $this->request->getPost('id_number') ?: null,
            'type'                   => $this->request->getPost('type'),
            'status'                 => $this->request->getPost('status'),
            'barangay_permit_no'     => $this->request->getPost('barangay_permit_no') ?: null,
            'barangay_permit_issued' => $this->request->getPost('barangay_permit_issued') ?: null,
            'barangay_permit_expiry' => $this->request->getPost('barangay_permit_expiry') ?: null,
        ];

        $model = new VendorModel();

        if ($id === null) {
            $data['vendor_no'] = $model->generateVendorNo();
            $model->insert($data);
            $newId = $model->getInsertID();
            (new AuditLogModel())->log('create', 'vendors', $newId, 'Registered vendor: ' . $data['vendor_no']);

            return redirect()->to('/vendors/view/' . $newId)
                ->with('success', 'Vendor registered successfully.');
        }

        $model->update($id, $data);
        (new AuditLogModel())->log('update', 'vendors', $id, 'Updated vendor');

        return redirect()->to('/vendors/view/' . $id)->with('success', 'Vendor updated successfully.');
    }

    public function delete(int $id): object
    {
        $guard = $this->requireAdmin();
        if ($guard !== true) {
            return $guard;
        }

        $model  = new VendorModel();
        $vendor = $model->find($id);
        if (! $vendor) {
            return redirect()->to('/vendors')->with('error', 'Vendor not found.');
        }

        (new AuditLogModel())->log('delete', 'vendors', $id, 'Deleted vendor: ' . $vendor['vendor_no']);
        $model->delete($id);

        return redirect()->to('/vendors')->with('success', 'Vendor deleted successfully.');
    }

    public function view(int $id): string|object
    {
        $vendor = (new VendorModel())->getDetail($id);
        if (! $vendor) {
            return redirect()->to('/vendors')->with('error', 'Vendor not found.');
        }

        $vsModel  = new VendorStallModel();
        $payModel = new PaymentModel();
        $overdue  = array_column((new VendorModel())->getOverdue(), 'assignment_id');

        $totalThisMonth = (float) ($payModel->db->table('payments')
            ->selectSum('amount_paid', 'total')
            ->where('vendor_id', $id)
            ->where('MONTH(payment_date)', date('m'))
            ->where('YEAR(payment_date)', date('Y'))
            ->get()->getRowArray()['total'] ?? 0);

        return view('vendors/view', $this->viewData([
            'page_title'       => $vendor['first_name'] . ' ' . $vendor['last_name'],
            'vendor'           => $vendor,
            'active_stalls'    => $vsModel->getActiveByVendor($id),
            'past_assignments' => array_filter($vsModel->getAllByVendor($id), static fn ($a) => $a['status'] !== 'active'),
            'payments'         => $payModel->getByVendor($id),
            'total_this_month' => $totalThisMonth,
            'overdue_ids'      => $overdue,
        ]));
    }
}
