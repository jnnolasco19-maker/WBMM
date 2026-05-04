<?php

namespace App\Controllers;

use App\Models\RecordModel;
use App\Models\VendorModel;
use App\Models\StallModel;

class RecordController extends BaseController
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

        $model    = new RecordModel();
        $search   = (string) $this->request->getGet('search');
        $type     = (string) $this->request->getGet('type');
        $dateFrom = (string) $this->request->getGet('date_from');
        $dateTo   = (string) $this->request->getGet('date_to');

        $records = $model->getFiltered($search, $type, $dateFrom, $dateTo)->paginate(15, 'records');

        return view('records/index', [
            'page_title' => 'Records',
            'user_name'  => session()->get('user_name'),
            'user_role'  => $role,
            'records'    => $records,
            'pager'      => $model->pager,
            'search'     => $search,
            'type'       => $type,
            'date_from'  => $dateFrom,
            'date_to'    => $dateTo,
        ]);
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    public function create(): string|object
    {
        $role = $this->getRole();
        if (! in_array($role, ['admin', 'staff'], true)) {
            session()->destroy();
            return redirect()->to(base_url('login'));
        }

        $vendorModel = new VendorModel();
        $stallModel  = new StallModel();

        return view('records/create', [
            'page_title' => 'Add Record',
            'user_name'  => session()->get('user_name'),
            'user_role'  => $role,
            'vendors'    => $vendorModel->where('status', 'active')->findAll(),
            'stalls'     => $stallModel->findAll(),
        ]);
    }

    public function store(): object
    {
        $role = $this->getRole();
        if (! in_array($role, ['admin', 'staff'], true)) {
            session()->destroy();
            return redirect()->to(base_url('login'));
        }

        $rules = [
            'vendor_id'   => 'required|integer',
            'type'        => 'required|in_list[payment,violation,renewal,other]',
            'record_date' => 'required|valid_date[Y-m-d]',
            'amount'      => 'permit_empty|decimal',
            'description' => 'permit_empty|max_length[1000]',
            'stall_id'    => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $vendorId = (int) $this->request->getPost('vendor_id');
        $vendorModel = new VendorModel();
        if (! $vendorModel->find($vendorId)) {
            return redirect()->back()->withInput()
                ->with('errors', ['vendor_id' => 'The selected vendor does not exist.']);
        }

        $recordDate = $this->request->getPost('record_date');
        if ($recordDate > date('Y-m-d')) {
            return redirect()->back()->withInput()
                ->with('errors', ['record_date' => 'Record date cannot be in the future.']);
        }

        $stallId = $this->request->getPost('stall_id');
        if ($stallId) {
            $stallModel = new StallModel();
            if (! $stallModel->find((int) $stallId)) {
                return redirect()->back()->withInput()
                    ->with('errors', ['stall_id' => 'The selected stall does not exist.']);
            }
        }

        $model = new RecordModel();
        $model->insert([
            'vendor_id'   => $vendorId,
            'stall_id'    => $stallId ?: null,
            'type'        => $this->request->getPost('type'),
            'amount'      => $this->request->getPost('amount') ?: null,
            'description' => $this->request->getPost('description'),
            'record_date' => $recordDate,
            'created_by'  => (int) session()->get('user_id'),
        ]);

        return redirect()->to(base_url('records'))
            ->with('message', 'Record created successfully.');
    }

    // -------------------------------------------------------------------------
    // EDIT / UPDATE
    // -------------------------------------------------------------------------

    public function edit(int $id): string|object
    {
        $guard = $this->requireAdmin();
        if ($guard !== null) return $guard;

        $model  = new RecordModel();
        $record = $model->find($id);

        if (! $record) {
            return redirect()->to(base_url('records'))
                ->with('error', 'Record not found.');
        }

        $vendorModel = new VendorModel();
        $stallModel  = new StallModel();

        return view('records/edit', [
            'page_title' => 'Edit Record',
            'user_name'  => session()->get('user_name'),
            'user_role'  => $this->getRole(),
            'record'     => $record,
            'vendors'    => $vendorModel->where('status', 'active')->findAll(),
            'stalls'     => $stallModel->findAll(),
        ]);
    }

    public function update(int $id): object
    {
        $guard = $this->requireAdmin();
        if ($guard !== null) return $guard;

        $model  = new RecordModel();
        $record = $model->find($id);

        if (! $record) {
            return redirect()->to(base_url('records'))
                ->with('error', 'Record not found.');
        }

        $rules = [
            'vendor_id'   => 'required|integer',
            'type'        => 'required|in_list[payment,violation,renewal,other]',
            'record_date' => 'required|valid_date[Y-m-d]',
            'amount'      => 'permit_empty|decimal',
            'description' => 'permit_empty|max_length[1000]',
            'stall_id'    => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $vendorId = (int) $this->request->getPost('vendor_id');
        $vendorModel = new VendorModel();
        if (! $vendorModel->find($vendorId)) {
            return redirect()->back()->withInput()
                ->with('errors', ['vendor_id' => 'The selected vendor does not exist.']);
        }

        $recordDate = $this->request->getPost('record_date');
        if ($recordDate > date('Y-m-d')) {
            return redirect()->back()->withInput()
                ->with('errors', ['record_date' => 'Record date cannot be in the future.']);
        }

        $stallId = $this->request->getPost('stall_id');
        if ($stallId) {
            $stallModel = new StallModel();
            if (! $stallModel->find((int) $stallId)) {
                return redirect()->back()->withInput()
                    ->with('errors', ['stall_id' => 'The selected stall does not exist.']);
            }
        }

        $model->update($id, [
            'vendor_id'   => $vendorId,
            'stall_id'    => $stallId ?: null,
            'type'        => $this->request->getPost('type'),
            'amount'      => $this->request->getPost('amount') ?: null,
            'description' => $this->request->getPost('description'),
            'record_date' => $recordDate,
        ]);

        return redirect()->to(base_url('records'))
            ->with('message', 'Record updated successfully.');
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    public function delete(int $id): object
    {
        $guard = $this->requireAdmin();
        if ($guard !== null) return $guard;

        $model  = new RecordModel();
        $record = $model->find($id);

        if (! $record) {
            return redirect()->to(base_url('records'))
                ->with('error', 'Record not found.');
        }

        $model->delete($id);

        return redirect()->to(base_url('records'))
            ->with('message', 'Record deleted successfully.');
    }
}
