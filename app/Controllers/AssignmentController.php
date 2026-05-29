<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\StallModel;
use App\Models\VendorModel;
use App\Models\VendorStallModel;

class AssignmentController extends BaseController
{
    private function requireAdminOrStaff(): bool|object
    {
        if (! in_array(session()->get('user_role'), ['admin', 'staff'], true)) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        return true;
    }

    public function index(): string|object
    {
        $guard = $this->requireAdminOrStaff();
        if ($guard !== true) {
            return $guard;
        }

        return view('assignments/index', $this->viewData([
            'page_title'  => 'Vendor-Stall Assignments',
            'assignments' => (new VendorStallModel())->getAll(),
        ]));
    }

    public function create(): string|object
    {
        $guard = $this->requireAdminOrStaff();
        if ($guard !== true) {
            return $guard;
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->saveAssignment();
        }

        $vendorModel = new VendorModel();
        $preVendorId = (int) $this->request->getGet('vendor_id');
        $preVendor   = $preVendorId ? $vendorModel->find($preVendorId) : null;
        $stallType   = $preVendor['type'] ?? '';

        return view('assignments/create', $this->viewData([
            'page_title' => 'New Assignment',
            'vendors'    => $vendorModel->getActiveForDropdown(),
            'stalls'     => (new StallModel())->getVacant($stallType),
            'pre_vendor' => $preVendor,
        ]));
    }

    private function saveAssignment(): object
    {
        $rules = [
            'vendor_id'     => 'required|integer',
            'stall_id'      => 'required|integer',
            'assigned_date' => 'required|valid_date[Y-m-d]',
            'permit_expiry' => 'permit_empty|valid_date[Y-m-d]',
            'permit_issued' => 'permit_empty|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $vendorId = (int) $this->request->getPost('vendor_id');
        $stallId  = (int) $this->request->getPost('stall_id');
        $vsModel  = new VendorStallModel();

        if ($vsModel->isStallOccupied($stallId)) {
            return redirect()->back()->withInput()->with('error', 'This stall is already occupied.');
        }

        $vendor = (new VendorModel())->find($vendorId);
        $stall  = (new StallModel())->find($stallId);

        if (! $vendor || ! $stall) {
            return redirect()->back()->withInput()->with('error', 'Invalid vendor or stall.');
        }

        if ($vendor['type'] !== $stall['type']) {
            return redirect()->back()->withInput()
                ->with('error', 'Vendor type must match stall type.');
        }

        $vsModel->insert([
            'vendor_id'     => $vendorId,
            'stall_id'      => $stallId,
            'permit_no'     => $this->request->getPost('permit_no') ?: null,
            'permit_issued' => $this->request->getPost('permit_issued') ?: null,
            'permit_expiry' => $this->request->getPost('permit_expiry') ?: null,
            'assigned_date' => $this->request->getPost('assigned_date'),
            'status'        => 'active',
            'notes'         => $this->request->getPost('notes') ?: null,
        ]);

        $assignId = $vsModel->getInsertID();
        (new StallModel())->update($stallId, ['status' => 'occupied']);
        (new AuditLogModel())->log('create', 'vendor_stalls', $assignId,
            'Assigned ' . $vendor['vendor_no'] . ' to ' . $stall['stall_code']);

        return redirect()->to('/assignments')->with('success', 'Assignment created successfully.');
    }

    public function terminate(int $id): object
    {
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('/assignments')
                ->with('error', 'Only administrators can terminate assignments.');
        }

        $vsModel = new VendorStallModel();
        $assign  = $vsModel->getDetail($id);

        if (! $assign || $assign['status'] !== 'active') {
            return redirect()->to('/assignments')->with('error', 'Assignment not found or already terminated.');
        }

        $vsModel->update($id, [
            'status'          => 'terminated',
            'terminated_date' => date('Y-m-d'),
        ]);

        (new StallModel())->update($assign['stall_id'], ['status' => 'vacant']);
        (new AuditLogModel())->log('terminate', 'vendor_stalls', $id,
            'Terminated assignment for stall ' . $assign['stall_code']);

        return redirect()->to('/assignments')->with('success', 'Assignment terminated.');
    }
}
