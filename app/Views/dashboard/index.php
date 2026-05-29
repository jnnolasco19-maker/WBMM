<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h3 mb-4">Dashboard</h1>

<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-2">
        <div class="card kpi-card"><div class="card-body">
            <div class="text-muted small">Active Vendors</div>
            <div class="h4 mb-0"><?= number_format($total_active_vendors) ?></div>
        </div></div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card kpi-card"><div class="card-body">
            <div class="text-muted small">Occupied Inside</div>
            <div class="h4 mb-0"><?= number_format($occupied_inside) ?></div>
        </div></div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card kpi-card"><div class="card-body">
            <div class="text-muted small">Occupied Outside</div>
            <div class="h4 mb-0"><?= number_format($occupied_outside) ?></div>
        </div></div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card kpi-card"><div class="card-body">
            <div class="text-muted small">Collections (Month)</div>
            <div class="h4 mb-0 text-success">₱<?= number_format($collections_this_month, 2) ?></div>
        </div></div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card kpi-card border-danger"><div class="card-body">
            <div class="text-muted small">Overdue Accounts</div>
            <div class="h4 mb-0 text-danger"><?= number_format($overdue_count) ?></div>
        </div></div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card kpi-card"><div class="card-body">
            <div class="text-muted small">Vacant Stalls</div>
            <div class="h4 mb-0"><?= number_format($total_vacant) ?></div>
        </div></div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white fw-semibold">Monthly Collections (Last 6 Months)</div>
    <div class="card-body"><canvas id="collectionsChart" height="100"></canvas></div>
</div>

<?php if (! empty($overdue_vendors)): ?>
<div class="alert alert-danger">
    <strong><i class="fa-solid fa-circle-exclamation"></i> Overdue Arkalaba</strong>
    <ul class="mb-0 mt-2">
        <?php foreach ($overdue_vendors as $o): ?>
        <li><a href="<?= base_url('vendors/view/' . $o['vendor_id']) ?>" class="alert-link"><?= esc($o['vendor_name']) ?></a>
            — <?= esc($o['stall_code']) ?> (<?= (int) $o['days_overdue'] ?> days overdue)</li>
        <?php endforeach; ?>
    </ul>
    <a href="<?= base_url('records/overdue') ?>" class="btn btn-sm btn-outline-danger mt-2">View all overdue</a>
</div>
<?php endif; ?>

<?php if (! empty($expiring_permits)): ?>
<div class="alert alert-warning">
    <strong><i class="fa-solid fa-id-card"></i> Permits Expiring (30 days)</strong>
    <ul class="mb-0 mt-2">
        <?php foreach (array_slice($expiring_permits, 0, 5) as $p): ?>
        <li><a href="<?= base_url('vendors/view/' . $p['vendor_id']) ?>" class="alert-link"><?= esc($p['vendor_name']) ?></a>
            — <?= esc($p['stall_code']) ?> expires <?= esc($p['permit_expiry']) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="alert alert-info">
    <strong><i class="fa-solid fa-door-open"></i> Vacant Stalls:</strong> <?= number_format($total_vacant) ?> revenue opportunities.
    <a href="<?= base_url('records/vacant') ?>" class="alert-link ms-2">View vacant stalls report →</a>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold">Recent Payments</div>
    <div class="table-responsive">
        <table class="table table-hover table-wbmm mb-0">
            <thead><tr>
                <th>Reference</th><th>Vendor</th><th>Stall</th><th>Type</th>
                <th>Amount</th><th>Collector</th><th>Date</th>
            </tr></thead>
            <tbody>
            <?php foreach ($recent_payments as $p): ?>
            <tr>
                <td><a href="<?= base_url('payments/receipt/' . $p['id']) ?>"><?= esc($p['reference_no']) ?></a></td>
                <td><?= esc($p['vendor_name']) ?></td>
                <td><?= esc($p['stall_code'] ?? '—') ?></td>
                <td><span class="badge badge-<?= esc($p['stall_type']) ?>"><?= esc($p['stall_type']) ?></span></td>
                <td>₱<?= number_format((float) $p['amount_paid'], 2) ?></td>
                <td><?= esc($p['collector_name']) ?></td>
                <td><?= date('M d, Y H:i', strtotime($p['payment_date'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($recent_payments)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">No payments recorded yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
new Chart(document.getElementById('collectionsChart'), {
    type: 'bar',
    data: {
        labels: <?= $chart_labels ?>,
        datasets: [
            { label: 'Inside', data: <?= $chart_inside ?>, backgroundColor: '#2563eb' },
            { label: 'Outside', data: <?= $chart_outside ?>, backgroundColor: '#059669' },
            { label: 'Ambulant', data: <?= $chart_ambulant ?>, backgroundColor: '#d97706' }
        ]
    },
    options: { responsive: true, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } } }
});
</script>
<?= $this->endSection() ?>
