<?php

namespace App\Controllers;

use App\Models\VendorModel;
use App\Models\PaymentModel;

class PaymentController extends BaseController
{
    public function index(): string|object
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // Fetch active vendors that have never paid or whose maximum covered period is in the past
        $overdueVendors = $db->table('vendors')
            ->select('vendors.*, MAX(payments.period_end) as last_payment_date')
            ->join('payments', 'payments.vendor_id = vendors.id', 'left')
            ->where('vendors.status', 'active')
            ->groupBy('vendors.id')
            ->having("last_payment_date IS NULL OR last_payment_date < '{$today}'")
            ->get()
            ->getResultArray();

        // Fetch list of payments processed
        $paymentModel = new PaymentModel();
        $payments = $paymentModel->getFilteredPayments()->paginate(15, 'payments');

        return view('payments/index', [
            'page_title'      => 'Arkalaba Collections',
            'user_name'       => session()->get('user_name'),
            'user_role'       => session()->get('user_role'),
            'overdue_vendors' => $overdueVendors,
            'payments'        => $payments,
            'pager'           => $paymentModel->pager
        ]);
    }

    public function create(): string|object
    {
        $vendorModel = new VendorModel();
        $vendors = $vendorModel->where('status', 'active')->findAll();

        return view('payments/create', [
            'page_title' => 'Record Rental Payment',
            'user_name'  => session()->get('user_name'),
            'user_role'  => session()->get('user_role'),
            'vendors'    => $vendors
        ]);
    }

    public function store(): object
    {
        $rules = [
            'vendor_id'    => 'required|integer',
            'amount'       => 'required|decimal',
            'payment_type' => 'required|in_list[daily,weekly,monthly]',
            'period_start' => 'required|valid_date[Y-m-d]',
            'period_end'   => 'required|valid_date[Y-m-d]',
            'notes'        => 'permit_empty|max_length[1000]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $vendorId    = (int) $this->request->getPost('vendor_id');
        $periodStart = $this->request->getPost('period_start');
        $periodEnd   = $this->request->getPost('period_end');

        if ($periodStart > $periodEnd) {
            return redirect()->back()->withInput()
                ->with('error', 'Period covered start date cannot be after the end date.');
        }

        $vendorModel = new VendorModel();
        $vendor = $vendorModel->find($vendorId);

        if (! $vendor) {
            return redirect()->back()->withInput()
                ->with('error', 'The selected vendor does not exist.');
        }

        $paymentModel = new PaymentModel();
        $refNo = $paymentModel->generateReferenceNo();

        $paymentModel->insert([
            'vendor_id'    => $vendorId,
            'amount'       => $this->request->getPost('amount'),
            'payment_type' => $this->request->getPost('payment_type'),
            'period_start' => $periodStart,
            'period_end'   => $periodEnd,
            'reference_no' => $refNo,
            'collected_by' => (int) session()->get('user_id'),
            'notes'        => $this->request->getPost('notes') ?: null
        ]);

        $paymentId = $paymentModel->getInsertID();
        $this->logActivity('Collected ' . $this->request->getPost('payment_type') . ' rental payment (' . $refNo . ') from vendor ' . $vendor['name'], 'payments', $paymentId);

        return redirect()->to('/payments')
            ->with('message', 'Rental payment of ₱' . number_format((float) $this->request->getPost('amount'), 2) . ' recorded successfully. Reference No: ' . $refNo);
    }

    public function receipt(int $id): string|object
    {
        $paymentModel = new PaymentModel();
        $payment = $paymentModel->getFilteredPayments()->find($id);

        if (! $payment) {
            return redirect()->to('/payments')->with('error', 'Payment transaction not found.');
        }

        return view('payments/receipt', [
            'page_title' => 'Print Receipt #' . $payment['reference_no'],
            'user_name'  => session()->get('user_name'),
            'user_role'  => session()->get('user_role'),
            'payment'    => $payment
        ]);
    }

    public function receiptPdf(int $id): object
    {
        $paymentModel = new PaymentModel();
        $payment = $paymentModel->getFilteredPayments()->find($id);

        if (! $payment) {
            return redirect()->to('/payments')->with('error', 'Payment transaction not found.');
        }

        // Render HTML view
        $html = view('payments/receipt_pdf', [
            'payment' => $payment
        ]);

        // Instantiate and configure Dompdf
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $dompdf = new \Dompdf\Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Stream the file for direct download
        $filename = 'Receipt_' . $payment['reference_no'] . '.pdf';
        $dompdf->stream($filename, [
            'Attachment' => 1
        ]);
        exit;
    }
}
