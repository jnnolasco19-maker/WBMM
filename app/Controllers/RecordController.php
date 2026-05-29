<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\StallModel;
use App\Models\UserModel;
use App\Models\VendorModel;

class RecordController extends BaseController
{
    private function requireRecordsAccess(): bool|object
    {
        if (! in_array(session()->get('user_role'), ['admin', 'supervisor', 'staff', 'collector'], true)) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        return true;
    }

    private function collectorFilter(): ?int
    {
        return session()->get('user_role') === 'collector'
            ? (int) session()->get('user_id')
            : null;
    }

    public function index(): string|object
    {
        $guard = $this->requireRecordsAccess();
        if ($guard !== true) {
            return $guard;
        }

        $paymentModel = new PaymentModel();
        $payments = $paymentModel->applyFilters(
            (string) $this->request->getGet('search'),
            (string) $this->request->getGet('stall_type'),
            (string) $this->request->getGet('payment_type'),
            (string) $this->request->getGet('collected_by'),
            (string) $this->request->getGet('date_from'),
            (string) $this->request->getGet('date_to'),
            (string) $this->request->getGet('vendor_id'),
            $this->collectorFilter()
        )->paginate(25);

        return view('records/index', $this->viewData([
            'page_title' => 'Transaction Records',
            'payments'   => $payments,
            'pager'      => $paymentModel->pager,
            'vendors'    => (new VendorModel())->getActiveForDropdown(),
            'collectors' => (new UserModel())->getActiveCollectors(),
            'filters'    => $this->request->getGet(),
        ]));
    }

    public function export(): object
    {
        $guard = $this->requireRecordsAccess();
        if ($guard !== true) {
            return $guard;
        }

        $payments = (new PaymentModel())->getFiltered(
            (string) $this->request->getGet('search'),
            (string) $this->request->getGet('stall_type'),
            (string) $this->request->getGet('payment_type'),
            (string) $this->request->getGet('collected_by'),
            (string) $this->request->getGet('date_from'),
            (string) $this->request->getGet('date_to'),
            (string) $this->request->getGet('vendor_id'),
            $this->collectorFilter()
        );

        return $this->csvDownload('WBMM_Records_' . date('Ymd_His') . '.csv', [
            'Reference No', 'Vendor', 'Vendor No', 'Stall', 'Type', 'Payment Type',
            'Computed', 'Paid', 'Period Start', 'Period End', 'Collected By', 'Date', 'Notes',
        ], $payments, static function (array $p): array {
            return [
                $p['reference_no'],
                $p['vendor_name'],
                $p['vendor_no'],
                $p['stall_code'] ?? '—',
                $p['stall_type'] ?? 'ambulant',
                $p['payment_type'],
                $p['computed_amount'],
                $p['amount_paid'],
                $p['period_start'],
                $p['period_end'],
                $p['collector_name'],
                $p['payment_date'],
                $p['notes'] ?? '',
            ];
        });
    }

    public function summary(): string|object
    {
        $guard = $this->requireRoles(['admin', 'supervisor']);
        if ($guard !== true) {
            return $guard;
        }

        $paymentModel = new PaymentModel();

        return view('records/summary', $this->viewData([
            'page_title'   => 'Financial Summary',
            'by_type'      => $paymentModel->getSummaryByStallType(),
            'by_section'   => $paymentModel->getSummaryBySection(),
            'by_month'     => $paymentModel->getSummaryByMonth(6),
        ]));
    }

    public function exportSummary(): object
    {
        $guard = $this->requireRoles(['admin', 'supervisor']);
        if ($guard !== true) {
            return $guard;
        }

        $paymentModel = new PaymentModel();
        $rows         = array_merge(
            array_map(static fn ($r) => ['Stall Type', $r['group_key'], $r['total_computed'], $r['total_paid'], $r['txn_count']], $paymentModel->getSummaryByStallType()),
            array_map(static fn ($r) => ['Section', $r['group_key'], $r['total_computed'], $r['total_paid'], $r['txn_count']], $paymentModel->getSummaryBySection()),
            array_map(static fn ($r) => ['Month', $r['group_key'], $r['total_computed'], $r['total_paid'], $r['txn_count']], $paymentModel->getSummaryByMonth(6))
        );

        return $this->csvDownload('WBMM_Summary_' . date('Ymd_His') . '.csv',
            ['Group', 'Label', 'Computed', 'Paid', 'Transactions'],
            $rows,
            static fn (array $r) => $r
        );
    }

    public function overdue(): string|object
    {
        $guard = $this->requireRecordsAccess();
        if ($guard !== true) {
            return $guard;
        }

        return view('records/overdue', $this->viewData([
            'page_title' => 'Overdue Arkalaba',
            'overdue'    => (new VendorModel())->getOverdue(),
        ]));
    }

    public function exportOverdue(): object
    {
        $guard = $this->requireRecordsAccess();
        if ($guard !== true) {
            return $guard;
        }

        $rows = (new VendorModel())->getOverdue();

        return $this->csvDownload('WBMM_Overdue_' . date('Ymd_His') . '.csv',
            ['Vendor', 'Vendor No', 'Stall', 'Section', 'Type', 'Last Payment', 'Days Overdue'],
            $rows,
            static fn (array $r) => [
                $r['vendor_name'], $r['vendor_no'], $r['stall_code'], $r['section'],
                $r['stall_type'], $r['last_payment_date'] ?? 'Never', $r['days_overdue'],
            ]
        );
    }

    public function vacant(): string|object
    {
        $guard = $this->requireRecordsAccess();
        if ($guard !== true) {
            return $guard;
        }

        return view('records/vacant', $this->viewData([
            'page_title' => 'Vacant Stalls',
            'stalls'     => (new StallModel())->getVacantReport(),
        ]));
    }

    public function exportVacant(): object
    {
        $guard = $this->requireRecordsAccess();
        if ($guard !== true) {
            return $guard;
        }

        $rows = (new StallModel())->getVacantReport();

        return $this->csvDownload('WBMM_Vacant_' . date('Ymd_His') . '.csv',
            ['Stall Code', 'Section', 'Type', 'SQM', 'Last Vendor', 'Vendor No'],
            $rows,
            static fn (array $r) => [
                $r['stall_code'], $r['section'], $r['type'], $r['sqm'] ?? '',
                $r['last_vendor_name'] ?? '', $r['last_vendor_no'] ?? '',
            ]
        );
    }

    public function permits(): string|object
    {
        $guard = $this->requireRecordsAccess();
        if ($guard !== true) {
            return $guard;
        }

        $days = (int) ($this->request->getGet('days') ?: 30);

        return view('records/permits', $this->viewData([
            'page_title' => 'Permit Expiry Report',
            'permits'    => (new VendorModel())->getExpiringPermits($days),
            'days'       => $days,
        ]));
    }

    public function exportPermits(): object
    {
        $guard = $this->requireRecordsAccess();
        if ($guard !== true) {
            return $guard;
        }

        $days = (int) ($this->request->getGet('days') ?: 30);
        $rows = (new VendorModel())->getExpiringPermits($days);

        return $this->csvDownload('WBMM_Permits_' . date('Ymd_His') . '.csv',
            ['Vendor', 'Vendor No', 'Stall', 'Permit No', 'Expiry', 'Days Remaining'],
            $rows,
            static fn (array $r) => [
                $r['vendor_name'], $r['vendor_no'], $r['stall_code'],
                $r['permit_no'] ?? '', $r['permit_expiry'], $r['days_remaining'],
            ]
        );
    }

    private function csvDownload(string $filename, array $headers, array $rows, callable $mapper): object
    {
        $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        foreach ($rows as $row) {
            fputcsv($output, $mapper($row));
        }
        fclose($output);
        exit;
    }
}
