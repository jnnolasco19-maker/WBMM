<?php

namespace App\Controllers;

use App\Models\VendorModel;
use App\Models\UserModel;
use App\Models\PaymentModel;
use App\Models\AuditLogModel;

class RecordController extends BaseController
{
    public function index(): string|object
    {
        $search      = (string) $this->request->getGet('search');
        $vendorId    = (string) $this->request->getGet('vendor_id');
        $paymentType = (string) $this->request->getGet('payment_type');
        $collectedBy = (string) $this->request->getGet('collected_by');
        $dateFrom    = (string) $this->request->getGet('date_from');
        $dateTo      = (string) $this->request->getGet('date_to');

        $vendorModel = new VendorModel();
        $userModel   = new UserModel();
        $paymentModel = new PaymentModel();

        // Paginated results for the list
        $payments = $paymentModel->getFilteredPayments($search, $vendorId, $paymentType, $collectedBy, $dateFrom, $dateTo)
                                 ->paginate(15, 'payments');

        // Unpaginated results to compute the summary totals
        $allFilteredPayments = $paymentModel->getFilteredPayments($search, $vendorId, $paymentType, $collectedBy, $dateFrom, $dateTo)
                                            ->findAll();

        $totalCollectionsSum = 0.00;
        foreach ($allFilteredPayments as $p) {
            $totalCollectionsSum += (float) $p['amount'];
        }

        return view('records/index', [
            'page_title'            => 'Records & Reports',
            'user_name'             => session()->get('user_name'),
            'user_role'             => session()->get('user_role'),
            'payments'              => $payments,
            'pager'                 => $paymentModel->pager,
            'vendors'               => $vendorModel->findAll(),
            'collectors'            => $userModel->where('role', 'staff')->findAll(),
            'total_collections_sum' => $totalCollectionsSum,
            'search'                => $search,
            'vendor_id'             => $vendorId,
            'payment_type'          => $paymentType,
            'collected_by'          => $collectedBy,
            'date_from'             => $dateFrom,
            'date_to'               => $dateTo
        ]);
    }

    public function export(): object
    {
        $search      = (string) $this->request->getGet('search');
        $vendorId    = (string) $this->request->getGet('vendor_id');
        $paymentType = (string) $this->request->getGet('payment_type');
        $collectedBy = (string) $this->request->getGet('collected_by');
        $dateFrom    = (string) $this->request->getGet('date_from');
        $dateTo      = (string) $this->request->getGet('date_to');

        $paymentModel = new PaymentModel();
        $payments = $paymentModel->getFilteredPayments($search, $vendorId, $paymentType, $collectedBy, $dateFrom, $dateTo)->findAll();

        $filename = 'WBMM_Collections_' . date('Ymd_His') . '.csv';

        // Set response headers for download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        // Column headers
        fputcsv($output, [
            'Reference No', 
            'Vendor Name', 
            'Stall Number', 
            'Amount', 
            'Payment Type', 
            'Period Covered', 
            'Collected By', 
            'Notes', 
            'Date Logged'
        ]);

        foreach ($payments as $payment) {
            fputcsv($output, [
                $payment['reference_no'],
                $payment['vendor_name'],
                $payment['stall_number'],
                number_format((float) $payment['amount'], 2),
                ucfirst($payment['payment_type']),
                $payment['period_start'] . ' to ' . $payment['period_end'],
                $payment['collector_name'] ?: 'System',
                $payment['notes'] ?: '',
                $payment['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    public function auditLogs(): string|object
    {
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access Denied. Admin privileges required.');
        }

        $auditLogModel = new AuditLogModel();
        $logs = $auditLogModel->getLogsWithUsers();

        return view('records/audit_logs', [
            'page_title' => 'System Audit Logs',
            'user_name'  => session()->get('user_name'),
            'user_role'  => session()->get('user_role'),
            'logs'       => $logs
        ]);
    }

    public function summary(): string|object
    {
        $db = \Config\Database::connect();

        // 1. Group by payment_type
        $byPaymentType = $db->table('payments')
            ->select('payment_type, COUNT(id) as count, SUM(amount) as total')
            ->groupBy('payment_type')
            ->get()
            ->getResultArray();

        // 2. Group by section
        $bySection = $db->table('payments')
            ->select('vendors.section, COUNT(payments.id) as count, SUM(payments.amount) as total')
            ->join('vendors', 'vendors.id = payments.vendor_id')
            ->groupBy('vendors.section')
            ->get()
            ->getResultArray();

        // 3. Group by month
        $byMonth = $db->table('payments')
            ->select("DATE_FORMAT(payments.created_at, '%Y-%m') as month_val, COUNT(id) as count, SUM(amount) as total")
            ->groupBy('month_val')
            ->orderBy('month_val', 'DESC')
            ->get()
            ->getResultArray();

        return view('records/summary', [
            'page_title'      => 'Financial Summary Report',
            'user_name'       => session()->get('user_name'),
            'user_role'       => session()->get('user_role'),
            'by_payment_type' => $byPaymentType,
            'by_section'      => $bySection,
            'by_month'        => $byMonth,
        ]);
    }

    public function exportSummary(): object
    {
        $db = \Config\Database::connect();

        // Query data
        $byPaymentType = $db->table('payments')
            ->select('payment_type, COUNT(id) as count, SUM(amount) as total')
            ->groupBy('payment_type')
            ->get()
            ->getResultArray();

        $bySection = $db->table('payments')
            ->select('vendors.section, COUNT(payments.id) as count, SUM(payments.amount) as total')
            ->join('vendors', 'vendors.id = payments.vendor_id')
            ->groupBy('vendors.section')
            ->get()
            ->getResultArray();

        $byMonth = $db->table('payments')
            ->select("DATE_FORMAT(payments.created_at, '%Y-%m') as month_val, COUNT(id) as count, SUM(amount) as total")
            ->groupBy('month_val')
            ->orderBy('month_val', 'DESC')
            ->get()
            ->getResultArray();

        $filename = 'WBMM_Financial_Summary_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        // Title
        fputcsv($output, ['FINANCIAL SUMMARY REPORT - GENERAL SANTOS CITY PUBLIC MARKET']);
        fputcsv($output, ['Generated on: ' . date('Y-m-d H:i:s')]);
        fputcsv($output, []);

        // 1. Payment Type Section
        fputcsv($output, ['1. BY COLLECTION TYPE']);
        fputcsv($output, ['Collection Type', 'Transaction Count', 'Total Amount (PHP)']);
        $totalCountPT = 0;
        $totalSumPT = 0.00;
        foreach ($byPaymentType as $pt) {
            fputcsv($output, [
                ucfirst($pt['payment_type']),
                $pt['count'],
                number_format((float) $pt['total'], 2, '.', '')
            ]);
            $totalCountPT += (int) $pt['count'];
            $totalSumPT += (float) $pt['total'];
        }
        fputcsv($output, ['GRAND TOTAL', $totalCountPT, number_format($totalSumPT, 2, '.', '')]);
        fputcsv($output, []);

        // 2. Market Section Section
        fputcsv($output, ['2. BY MARKET SECTION']);
        fputcsv($output, ['Market Section', 'Transaction Count', 'Total Amount (PHP)']);
        $totalCountSec = 0;
        $totalSumSec = 0.00;
        foreach ($bySection as $sec) {
            fputcsv($output, [
                $sec['section'],
                $sec['count'],
                number_format((float) $sec['total'], 2, '.', '')
            ]);
            $totalCountSec += (int) $sec['count'];
            $totalSumSec += (float) $sec['total'];
        }
        fputcsv($output, ['GRAND TOTAL', $totalCountSec, number_format($totalSumSec, 2, '.', '')]);
        fputcsv($output, []);

        // 3. Monthly Trends Section
        fputcsv($output, ['3. BY CALENDAR MONTH']);
        fputcsv($output, ['Month', 'Transaction Count', 'Total Amount (PHP)']);
        $totalCountMon = 0;
        $totalSumMon = 0.00;
        foreach ($byMonth as $mon) {
            $formattedMonth = date('F Y', strtotime($mon['month_val'] . '-01'));
            fputcsv($output, [
                $formattedMonth,
                $mon['count'],
                number_format((float) $mon['total'], 2, '.', '')
            ]);
            $totalCountMon += (int) $mon['count'];
            $totalSumMon += (float) $mon['total'];
        }
        fputcsv($output, ['GRAND TOTAL', $totalCountMon, number_format($totalSumMon, 2, '.', '')]);

        fclose($output);
        exit;
    }
}
