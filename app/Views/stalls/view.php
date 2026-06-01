<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-2">Stall <?= esc($stall['stall_code']) ?></h1>
<p class="text-muted"><?= esc($stall['section']) ?> · <?= esc($stall['type']) ?> · <span class="badge bg-secondary"><?= esc($stall['status']) ?></span></p>

<div class="row g-4">
<div class="col-md-6">
<div class="card mb-4"><div class="card-header">Stall Details</div><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-5">SQM</dt><dd class="col-7"><?= $stall['sqm'] ? esc($stall['sqm']) : '—' ?></dd>
        <dt class="col-5">Floor</dt><dd class="col-7"><?= esc($stall['floor_level'] ?? '—') ?></dd>
        <dt class="col-5">Notes</dt><dd class="col-7"><?= esc($stall['notes'] ?? '—') ?></dd>
    </dl>
</div></div>

<?php if (! empty($stall['vendor_name'])): ?>
<div class="card mb-4 border-success"><div class="card-header bg-success text-white">Current Vendor</div><div class="card-body">
    <p><a href="<?= base_url('vendors/view/' . $stall['vendor_id']) ?>"><strong><?= esc($stall['vendor_name']) ?></strong></a> (<?= esc($stall['vendor_no']) ?>)</p>
    <p class="mb-0 small">Permit: <?= esc($stall['permit_no'] ?? '—') ?> · Expiry: <?= esc($stall['permit_expiry'] ?? '—') ?></p>
</div></div>
<?php endif; ?>

<div class="card mb-4 text-center d-print-block" id="printableQrCard">
    <div class="card-header bg-primary text-white d-print-none">Stall QR Code</div>
    <div class="card-body py-4">
        <h5 class="fw-bold mb-1">Stall <?= esc($stall['stall_code']) ?></h5>
        <p class="text-muted small mb-3"><?= esc($stall['section']) ?> Section · <?= esc(ucfirst($stall['type'])) ?></p>
        <div class="d-flex justify-content-center mb-3">
            <canvas id="qrCodeCanvas"></canvas>
        </div>
        <div class="d-print-none">
            <button onclick="window.print()" class="btn btn-outline-primary btn-sm btn-print-qr">
                <i class="fa-solid fa-print me-1"></i> Print Stall Tag
            </button>
        </div>
    </div>
</div>
</div>

<div class="col-md-6">
<div class="card mb-4"><div class="card-header">Assignment History</div>
<div class="table-responsive"><table class="table table-sm mb-0">
<thead><tr><th>Vendor</th><th>Assigned</th><th>Status</th></tr></thead>
<tbody>
<?php foreach ($assignments as $a): ?>
<tr><td><?= esc($a['vendor_name']) ?></td><td><?= esc($a['assigned_date']) ?></td><td><?= esc($a['status']) ?></td></tr>
<?php endforeach; ?>
</tbody></table></div></div>

<div class="card"><div class="card-header">Payment History</div>
<div class="table-responsive"><table class="table table-sm mb-0">
<thead><tr><th>Ref</th><th>Amount</th><th>Date</th></tr></thead>
<tbody>
<?php foreach ($payments as $p): ?>
<tr><td><a href="<?= base_url('payments/receipt/' . $p['id']) ?>"><?= esc($p['reference_no']) ?></a></td>
<td>₱<?= number_format((float)$p['amount_paid'],2) ?></td><td><?= date('M d, Y', strtotime($p['payment_date'])) ?></td></tr>
<?php endforeach; ?>
<?php if (empty($payments)): ?><tr><td colspan="3" class="text-muted text-center">No payments</td></tr><?php endif; ?>
</tbody></table></div></div>
</div>
</div>
<a href="<?= base_url('stalls') ?>" class="btn btn-outline-secondary">← Back</a>

<?= $this->section('scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        new QRious({
            element: document.getElementById('qrCodeCanvas'),
            value: '<?= base_url("payments/create?stall_id=" . $stall['id'] . (!empty($stall['vendor_id']) ? "&vendor_id=" . $stall['vendor_id'] : "")) ?>',
            size: 180,
            level: 'H'
        });
    });
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
