<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\RateModel;
use App\Models\UserModel;
use App\Models\VendorModel;
use App\Models\VendorStallModel;

class PaymentController extends BaseController
{
    private function canCollect(): bool
    {
        return in_array(session()->get('user_role'), ['admin', 'collector'], true);
    }

    public function index(): string|object
    {
        $role = session()->get('user_role');
        $collectorOnly = ($role === 'collector') ? (int) session()->get('user_id') : null;

        $paymentModel = new PaymentModel();
        $payments = $paymentModel
            ->applyFilters(
                (string) $this->request->getGet('search'),
                (string) $this->request->getGet('stall_type'),
                (string) $this->request->getGet('payment_type'),
                (string) $this->request->getGet('collected_by'),
                (string) $this->request->getGet('date_from'),
                (string) $this->request->getGet('date_to'),
                (string) $this->request->getGet('vendor_id'),
                $collectorOnly
            )
            ->paginate(20);

        return view('payments/index', $this->viewData([
            'page_title' => 'Arkalaba Collections',
            'payments'   => $payments,
            'pager'      => $paymentModel->pager,
        ]));
    }

    public function create(): string|object
    {
        if (! $this->canCollect() && ! in_array(session()->get('user_role'), ['supervisor', 'staff'], true)) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->storePayment();
        }

        $vendorModel = new VendorModel();
        $userModel   = new UserModel();
        $rateModel   = new RateModel();
        $currentRate = $rateModel->getCurrent();

        return view('payments/create', $this->viewData([
            'page_title'      => 'Collect Arkalaba',
            'vendors'         => $vendorModel->getActiveForDropdown(),
            'collectors'      => $userModel->getActiveCollectors(),
            'current_rate'    => $currentRate,
            'query_vendor_id' => (int) $this->request->getGet('vendor_id'),
            'query_stall_id'  => (int) $this->request->getGet('stall_id'),
            'can_collect'     => $this->canCollect(),
        ]));
    }

    private function storePayment(): object
    {
        if (! $this->canCollect()) {
            return redirect()->to('/payments')->with('error', 'Only collectors and administrators can record payments.');
        }

        $rules = [
            'vendor_id'      => 'required|integer',
            'payment_type'   => 'required|in_list[daily,monthly]',
            'period_start'   => 'required|valid_date[Y-m-d]',
            'period_end'     => 'required|valid_date[Y-m-d]',
            'computed_amount'=> 'required|decimal',
            'amount_paid'    => 'required|decimal',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $vendor = (new VendorModel())->find((int) $this->request->getPost('vendor_id'));
        if (! $vendor || $vendor['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Invalid vendor selected.');
        }

        $paymentType = $this->request->getPost('payment_type');
        if (in_array($vendor['type'], ['inside', 'outside'], true) && $paymentType !== 'monthly') {
            return redirect()->back()->withInput()->with('error', 'Permanent stall holders can only pay monthly.');
        }
        if ($vendor['type'] === 'ambulant' && $paymentType !== 'daily') {
            return redirect()->back()->withInput()->with('error', 'Ambulant vendors can only pay daily.');
        }

        $stallId = (int) $this->request->getPost('stall_id');
        if ($vendor['type'] !== 'ambulant' && $stallId <= 0) {
            return redirect()->back()->withInput()->with('error', 'Please select a stall for this vendor.');
        }

        $rate = (new RateModel())->getCurrent();
        if (! $rate) {
            return redirect()->back()->withInput()->with('error', 'No active rate configured. Contact the administrator.');
        }

        $computed = (float) $this->request->getPost('computed_amount');
        $paid     = (float) $this->request->getPost('amount_paid');
        $notes    = trim((string) $this->request->getPost('notes'));

        if ($paid < $computed && $notes === '') {
            return redirect()->back()->withInput()
                ->with('warning', 'Underpayment detected. Please provide a note explaining the short payment.');
        }

        $collectedBy = (int) (session()->get('user_role') === 'admin'
            ? ($this->request->getPost('collected_by') ?: session()->get('user_id'))
            : session()->get('user_id'));

        $stallType = $vendor['type'];
        $sqm       = null;
        $rateUsed  = (float) $this->request->getPost('rate_used');

        if ($stallId > 0) {
            $vs = (new VendorStallModel())->getActiveByStall($stallId);
            if (! $vs || (int) $vs['vendor_id'] !== (int) $vendor['id']) {
                return redirect()->back()->withInput()->with('error', 'Invalid stall assignment for this vendor.');
            }
            $stall = (new \App\Models\StallModel())->find($stallId);
            $stallType = $stall['type'];
            $sqm       = $stall['sqm'] ?? null;
        }

        $paymentModel = new PaymentModel();
        $referenceNo    = $paymentModel->generateReferenceNo();
        $paymentModel->insert([
            'reference_no'    => $referenceNo,
            'vendor_id'       => (int) $vendor['id'],
            'stall_id'        => $stallId > 0 ? $stallId : null,
            'rate_id'         => (int) $rate['id'],
            'payment_type'    => $this->request->getPost('payment_type'),
            'sqm_charged'     => $sqm,
            'rate_used'       => $rateUsed,
            'computed_amount' => $computed,
            'amount_paid'     => $paid,
            'period_start'    => $this->request->getPost('period_start'),
            'period_end'      => $this->request->getPost('period_end'),
            'collected_by'    => $collectedBy,
            'notes'           => $notes ?: null,
        ]);

        $paymentId = $paymentModel->getInsertID();
        $this->audit('payment', 'payments', $paymentId, 'Collected arkalaba ' . $referenceNo);

        return redirect()->to('/payments/receipt/' . $paymentId)
            ->with('success', 'Payment recorded successfully.');
    }

    public function ajaxVendor(int $vendorId): \CodeIgniter\HTTP\ResponseInterface
    {
        $vendor = (new VendorModel())->find($vendorId);
        if (! $vendor) {
            return $this->response->setJSON(['error' => 'Vendor not found'])->setStatusCode(404);
        }

        $stalls = (new VendorStallModel())->getActiveByVendor($vendorId);
        $rate   = (new RateModel())->getCurrent();

        return $this->response->setJSON([
            'vendor' => $vendor,
            'stalls' => $stalls,
            'rate'   => $rate,
        ]);
    }

    public function ajaxCompute(): \CodeIgniter\HTTP\ResponseInterface
    {
        $stallType   = $this->request->getGet('stall_type');
        $paymentType = $this->request->getGet('payment_type');
        $sqm         = (float) $this->request->getGet('sqm');

        $rate = (new RateModel())->getCurrent();
        if (! $rate) {
            return $this->response->setJSON(['error' => 'No active rate'])->setStatusCode(400);
        }

        $amount   = (new RateModel())->compute($rate, $stallType, $paymentType, $sqm);
        $rateUsed = $this->resolveRateUsed($rate, $stallType, $paymentType, $sqm);

        return $this->response->setJSON([
            'computed_amount' => $amount,
            'rate_used'       => $rateUsed,
        ]);
    }

    private function resolveRateUsed(array $rate, string $stallType, string $paymentType, float $sqm): float
    {
        if ($stallType === 'inside') {
            return (float) $rate['inside_rate_per_sqm'];
        }
        if ($stallType === 'outside') {
            return (float) $rate['outside_monthly_rate'];
        }

        return (float) $rate['ambulant_daily_rate'];
    }

    public function receipt(int $id): string|object
    {
        $payment = (new PaymentModel())->getDetail($id);
        if (! $payment) {
            return redirect()->to('/payments')->with('error', 'Receipt not found.');
        }

        if (session()->get('user_role') === 'collector'
            && (int) $payment['collected_by'] !== (int) session()->get('user_id')) {
            return redirect()->to('/payments')->with('error', 'Access denied.');
        }

        return view('payments/receipt', $this->viewData([
            'page_title' => 'Resibo — ' . $payment['reference_no'],
            'payment'    => $payment,
        ]));
    }

    public function receiptPdf(int $id): object
    {
        $payment = (new PaymentModel())->getDetail($id);
        if (! $payment) {
            return redirect()->to('/payments')->with('error', 'Receipt not found.');
        }

        $html = view('payments/receipt_pdf', ['payment' => $payment]);

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('Receipt_' . $payment['reference_no'] . '.pdf', ['Attachment' => true]);
        exit;
    }
}
