<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0">Collector Remittance Report</h1>
    <a href="<?= base_url('reports/collector/export') ?>" class="btn btn-outline-success">Export CSV</a>
</div>
<div class="card"><div class="table-responsive">
<table class="table table-hover table-wbmm mb-0">
<thead><tr>
    <th>Collector</th><th>Today</th><th>This Week</th><th>This Month</th>
    <th>Total Computed</th><th>Total Paid</th><th>Difference</th>
</tr></thead>
<tbody>
<?php foreach ($summaries as $s): ?>
<tr>
    <td><a href="<?= base_url('reports/collector/'.$s['collector_id']) ?>"><?= esc($s['collector_name']) ?></a></td>
    <td><?= (int)$s['collections_today'] ?></td>
    <td><?= (int)$s['collections_week'] ?></td>
    <td><?= (int)$s['collections_month'] ?></td>
    <td>₱<?= number_format((float)$s['total_computed'],2) ?></td>
    <td>₱<?= number_format((float)$s['total_paid'],2) ?></td>
    <td class="<?= (float)$s['difference']<0?'text-danger':'text-success' ?>">
        ₱<?= number_format((float)$s['difference'],2) ?>
    </td>
</tr>
<?php endforeach; ?>
<?php if (empty($summaries)): ?><tr><td colspan="7" class="text-center text-muted py-4">No collector data.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?= $this->endSection() ?>
