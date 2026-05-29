<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-4">Remittance Detail — <?= esc($collector_name) ?></h1>
<a href="<?= base_url('reports/collector') ?>" class="btn btn-outline-secondary mb-3">← Back to Summary</a>
<div class="card"><div class="table-responsive">
<table class="table table-hover table-wbmm mb-0">
<thead><tr><th>Date</th><th>Reference</th><th>Vendor</th><th>Stall</th><th>Computed</th><th>Paid</th><th>Diff</th><th>Notes</th></tr></thead>
<tbody>
<?php foreach ($payments as $p): ?>
<?php $diff = (float)$p['amount_paid'] - (float)$p['computed_amount']; ?>
<tr>
    <td><?= date('M d, Y', strtotime($p['payment_date'])) ?></td>
    <td><a href="<?= base_url('payments/receipt/'.$p['id']) ?>"><?= esc($p['reference_no']) ?></a></td>
    <td><?= esc($p['vendor_name']) ?></td>
    <td><?= esc($p['stall_code'] ?? '—') ?></td>
    <td>₱<?= number_format((float)$p['computed_amount'],2) ?></td>
    <td>₱<?= number_format((float)$p['amount_paid'],2) ?></td>
    <td class="<?= $diff<0?'text-danger':'' ?>">₱<?= number_format($diff,2) ?></td>
    <td class="small"><?= esc($p['notes'] ?? '') ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?= $this->endSection() ?>
