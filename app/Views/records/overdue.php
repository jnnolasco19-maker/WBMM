<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0">Overdue Arkalaba Report</h1>
    <a href="<?= base_url('records/overdue/export') ?>" class="btn btn-outline-success">Export CSV</a>
</div>
<div class="card"><div class="table-responsive">
<table class="table table-hover table-wbmm mb-0">
<thead><tr><th>Vendor</th><th>Vendor No</th><th>Stall</th><th>Section</th><th>Type</th><th>Last Payment</th><th>Days Overdue</th></tr></thead>
<tbody>
<?php foreach ($overdue as $o): ?>
<tr class="table-danger">
    <td><a href="<?= base_url('vendors/view/'.$o['vendor_id']) ?>"><?= esc($o['vendor_name']) ?></a></td>
    <td><?= esc($o['vendor_no']) ?></td>
    <td><?= esc($o['stall_code']) ?></td>
    <td><?= esc($o['section']) ?></td>
    <td><?= esc($o['stall_type']) ?></td>
    <td><?= esc($o['last_payment_date'] ?? 'Never') ?></td>
    <td><strong><?= (int)$o['days_overdue'] ?></strong></td>
</tr>
<?php endforeach; ?>
<?php if (empty($overdue)): ?><tr><td colspan="7" class="text-center text-muted py-4">No overdue accounts.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?= $this->endSection() ?>
