<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\RateModel;

class RateController extends BaseController
{
    public function index(): string|object
    {
        $guard = $this->requireRoles(['admin']);
        if ($guard !== true) {
            return $guard;
        }

        $model = new RateModel();

        return view('rates/index', $this->viewData([
            'page_title' => 'Rate Management',
            'rates'      => $model->getAllWithUsage(),
            'current'    => $model->getCurrent(),
        ]));
    }

    public function create(): string|object
    {
        $guard = $this->requireRoles(['admin']);
        if ($guard !== true) {
            return $guard;
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->saveRate();
        }

        return view('rates/create', $this->viewData([
            'page_title' => 'Set New Rate',
            'current'    => (new RateModel())->getCurrent(),
        ]));
    }

    private function saveRate(): object
    {
        $rules = [
            'inside_rate_per_sqm'  => 'required|decimal|greater_than[0]',
            'outside_daily_rate'   => 'required|decimal|greater_than[0]',
            'outside_weekly_rate'  => 'required|decimal|greater_than[0]',
            'outside_monthly_rate' => 'required|decimal|greater_than[0]',
            'ambulant_daily_rate'  => 'required|decimal|greater_than[0]',
            'effective_date'       => 'required|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $effectiveDate = $this->request->getPost('effective_date');
        if ($effectiveDate < date('Y-m-d')) {
            session()->setFlashdata('warning',
                'The effective date is in the past. This rate will apply retroactively from that date.');
        }

        $data = [
            'inside_rate_per_sqm'  => $this->request->getPost('inside_rate_per_sqm'),
            'outside_daily_rate'   => $this->request->getPost('outside_daily_rate'),
            'outside_weekly_rate'  => $this->request->getPost('outside_weekly_rate'),
            'outside_monthly_rate' => $this->request->getPost('outside_monthly_rate'),
            'ambulant_daily_rate'  => $this->request->getPost('ambulant_daily_rate'),
            'effective_date'       => $effectiveDate,
            'created_by'           => session()->get('user_id'),
        ];

        $model = new RateModel();
        $model->insert($data);
        $id = $model->getInsertID();

        (new AuditLogModel())->log('create', 'rates', $id, 'New rate effective ' . $effectiveDate);

        return redirect()->to('/rates')->with('success', 'New rate saved successfully.');
    }
}
