<?php

namespace App\Controllers;

use App\Models\PaymentModel;

class ReportController extends BaseController
{
    public function collector(): string|object
    {
        $guard = $this->requireRoles(['admin', 'supervisor']);
        if ($guard !== true) {
            return $guard;
        }

        return view('reports/collector', $this->viewData([
            'page_title' => 'Collector Remittance Report',
            'summaries'  => (new PaymentModel())->getCollectorSummary(),
        ]));
    }

    public function collectorDetail(int $id): string|object
    {
        $guard = $this->requireRoles(['admin', 'supervisor']);
        if ($guard !== true) {
            return $guard;
        }

        $payments = (new PaymentModel())->getByCollector($id);
        $user     = (new \App\Models\UserModel())->find($id);
        $collectorName = $user['name'] ?? 'Collector';

        return view('reports/collector_detail', $this->viewData([
            'page_title'     => 'Remittance — ' . $collectorName,
            'collector_id'   => $id,
            'collector_name' => $collectorName,
            'payments'       => $payments,
        ]));
    }

    public function collectorExport(): object
    {
        $guard = $this->requireRoles(['admin', 'supervisor']);
        if ($guard !== true) {
            return $guard;
        }

        $summaries = (new PaymentModel())->getCollectorSummary();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=WBMM_Collector_Remittance_' . date('Ymd_His') . '.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Collector', 'Today', 'This Week', 'This Month', 'Total Computed', 'Total Paid', 'Difference']);
        foreach ($summaries as $s) {
            fputcsv($output, [
                $s['collector_name'],
                $s['collections_today'],
                $s['collections_week'],
                $s['collections_month'],
                $s['total_computed'],
                $s['total_paid'],
                $s['difference'],
            ]);
        }
        fclose($output);
        exit;
    }
}
