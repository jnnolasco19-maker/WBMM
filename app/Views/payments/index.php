<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0">Arkalaba Collections</h1>
    <?php if (in_array($user_role, ['admin','collector'], true)): ?>
    <a href="<?= base_url('payments/create') ?>" class="btn btn-success"><i class="fa-solid fa-plus"></i> Collect Arkalaba</a>
    <?php endif; ?>
</div>
<div class="card"><div class="table-responsive">
<table class="table table-hover table-wbmm mb-0">
<thead><tr><th>Reference</th><th>Vendor</th><th>Stall</th><th>Type</th><th>Computed</th><th>Paid</th><th>Collector</th><th>Date</th></tr></thead>
<tbody>
<?php foreach ($payments as $p): ?>
<tr>
    <td><a href="<?= base_url('payments/receipt/'.$p['id']) ?>"><?= esc($p['reference_no']) ?></a></td>
    <td><?= esc($p['vendor_name']) ?></td>
    <td><?= esc($p['stall_code'] ?? '—') ?></td>
    <td><?= esc($p['stall_type'] ?? 'ambulant') ?></td>
    <td>₱<?= number_format((float)$p['computed_amount'],2) ?></td>
    <td class="<?= (float)$p['amount_paid']<(float)$p['computed_amount']?'text-danger fw-bold':'' ?>">₱<?= number_format((float)$p['amount_paid'],2) ?></td>
    <td><?= esc($p['collector_name']) ?></td>
    <td><?= date('M d, Y', strtotime($p['payment_date'])) ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<div class="card-footer"><?= $pager->links() ?></div></div>
<?= $this->endSection() ?>
