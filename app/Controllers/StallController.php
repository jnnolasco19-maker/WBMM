<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\PaymentModel;
use App\Models\StallModel;
use App\Models\VendorStallModel;

class StallController extends BaseController
{
    private function requireAdmin(): bool|object
    {
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('/stalls')
                ->with('error', 'Only administrators can perform this action.');
        }

        return true;
    }

    public function index(): string
    {
        $model = new StallModel();

        return view('stalls/index', $this->viewData([
            'page_title' => 'Stalls',
            'stalls'     => $model->getFiltered(
                (string) $this->request->getGet('type'),
                (string) $this->request->getGet('section'),
                (string) $this->request->getGet('status'),
                (string) $this->request->getGet('search')
            ),
            'sections'   => $model->getSections(),
            'type'       => (string) $this->request->getGet('type'),
            'section'    => (string) $this->request->getGet('section'),
            'status'     => (string) $this->request->getGet('status'),
            'search'     => (string) $this->request->getGet('search'),
        ]));
    }

    public function create(): string|object
    {
        $guard = $this->requireAdmin();
        if ($guard !== true) {
            return $guard;
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->saveStall();
        }

        return view('stalls/create', $this->viewData(['page_title' => 'Add Stall']));
    }

    public function edit(int $id): string|object
    {
        $guard = $this->requireAdmin();
        if ($guard !== true) {
            return $guard;
        }

        $stall = (new StallModel())->find($id);
        if (! $stall) {
            return redirect()->to('/stalls')->with('error', 'Stall not found.');
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->saveStall($id, $stall);
        }

        return view('stalls/edit', $this->viewData([
            'page_title' => 'Edit Stall',
            'stall'      => $stall,
        ]));
    }

    private function saveStall(?int $id = null, ?array $existing = null): object
    {
        $type = $this->request->getPost('type');
        $rules = [
            'stall_code'             => $id
                ? "required|max_length[50]|is_unique[stalls.stall_code,id,{$id}]"
                : 'required|max_length[50]|is_unique[stalls.stall_code]',
            'section'                => 'required|max_length[100]',
            'type'                   => 'required|in_list[inside,outside,ambulant]',
            'barangay_permit_no'     => 'permit_empty|max_length[50]',
            'barangay_permit_issued' => 'permit_empty|valid_date[Y-m-d]',
            'barangay_permit_expiry' => 'permit_empty|valid_date[Y-m-d]',
        ];

        if ($id === null) {
            $rules['status'] = 'required|in_list[vacant,suspended]';
        }

        if (in_array($type, ['inside', 'outside'], true)) {
            $rules['sqm'] = 'required|decimal|greater_than[0]';
        }
        if ($type === 'inside') {
            $rules['floor_level'] = 'required|max_length[20]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'stall_code'             => strtoupper(trim($this->request->getPost('stall_code'))),
            'section'                => $this->request->getPost('section'),
            'type'                   => $type,
            'sqm'                    => in_array($type, ['inside', 'outside'], true) ? $this->request->getPost('sqm') : null,
            'floor_level'            => $type === 'inside' ? $this->request->getPost('floor_level') : null,
            'notes'                  => $this->request->getPost('notes') ?: null,
            'barangay_permit_no'     => $this->request->getPost('barangay_permit_no') ?: null,
            'barangay_permit_issued' => $this->request->getPost('barangay_permit_issued') ?: null,
            'barangay_permit_expiry' => $this->request->getPost('barangay_permit_expiry') ?: null,
        ];

        if ($id === null) {
            $data['status'] = $this->request->getPost('status');
        } else {
            $newStatus = $this->request->getPost('status');
            if ($newStatus !== 'occupied') {
                $data['status'] = $newStatus;
            }
        }

        $model = new StallModel();
        if ($id === null) {
            $model->insert($data);
            $newId = $model->getInsertID();
            (new AuditLogModel())->log('create', 'stalls', $newId, 'Created stall: ' . $data['stall_code']);

            return redirect()->to('/stalls')->with('success', 'Stall created successfully.');
        }

        $model->update($id, $data);
        (new AuditLogModel())->log('update', 'stalls', $id, 'Updated stall: ' . $data['stall_code']);

        return redirect()->to('/stalls')->with('success', 'Stall updated successfully.');
    }

    public function delete(int $id): object
    {
        $guard = $this->requireAdmin();
        if ($guard !== true) {
            return $guard;
        }

        $model = new StallModel();
        $stall = $model->find($id);
        if (! $stall) {
            return redirect()->to('/stalls')->with('error', 'Stall not found.');
        }

        if ((new VendorStallModel())->isStallOccupied($id)) {
            return redirect()->to('/stalls')
                ->with('error', 'Cannot delete a stall with an active vendor assignment.');
        }

        (new AuditLogModel())->log('delete', 'stalls', $id, 'Deleted stall: ' . $stall['stall_code']);
        $model->delete($id);

        return redirect()->to('/stalls')->with('success', 'Stall deleted successfully.');
    }

    public function view(int $id): string|object
    {
        $stall = (new StallModel())->getDetail($id);
        if (! $stall) {
            return redirect()->to('/stalls')->with('error', 'Stall not found.');
        }

        return view('stalls/view', $this->viewData([
            'page_title'  => 'Stall ' . $stall['stall_code'],
            'stall'       => $stall,
            'assignments' => (new VendorStallModel())->getAllByStall($id),
            'payments'    => (new PaymentModel())->getByStall($id),
        ]));
    }
}
