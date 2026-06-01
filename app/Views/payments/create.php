<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-4">Collect Arkalaba</h1>
<?php if (! $can_collect): ?>
<div class="alert alert-info">You have view-only access. Contact a collector or administrator to record payments.</div>
<?php endif; ?>
<div class="card"><div class="card-body">
<form method="post" action="<?= base_url('payments/create') ?>" id="paymentForm">
<?= csrf_field() ?>
<input type="hidden" id="stall_type_val" name="stall_type_hidden" value="">
<input type="hidden" id="sqm_val" value="0">
<input type="hidden" id="rate_used" name="rate_used" value="">
<input type="hidden" id="preselect_stall_id" value="<?= (int) ($query_stall_id ?? 0) ?>">

<h5 class="text-muted mb-3">Step 1 — Select Vendor</h5>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">Search vendor</label>
        <input type="text" id="vendor_search" class="form-control" placeholder="Name or vendor no..." <?= $can_collect ? '' : 'disabled' ?>>
    </div>
    <div class="col-md-5">
        <label class="form-label">Vendor *</label>
        <select name="vendor_id" id="vendor_id" class="form-select" required <?= $can_collect ? '' : 'disabled' ?>>
            <option value="">— Select vendor —</option>
            <?php foreach ($vendors as $v): ?>
            <option value="<?= $v['id'] ?>" <?= ($query_vendor_id ?? 0) == $v['id'] ? 'selected' : '' ?>>
                <?= esc($v['vendor_no'].' — '.$v['first_name'].' '.$v['last_name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <span id="vendor_type_badge" class="badge bg-secondary mb-2">—</span>
    </div>
</div>

<h5 class="text-muted mb-3">Step 2 — Select Stall</h5>
<div id="stall_section" class="row g-3 mb-4">
    <div class="col-md-8">
        <select name="stall_id" id="stall_id" class="form-select" <?= $can_collect ? '' : 'disabled' ?>>
            <option value="">— Select stall —</option>
        </select>
        <small class="text-muted">Skipped for ambulant vendors (no fixed stall).</small>
    </div>
</div>

<h5 class="text-muted mb-3">Step 3 — Payment Details</h5>
<div class="row g-3">
    <div class="col-md-3"><label class="form-label">Payment Type</label>
        <select name="payment_type" id="payment_type" class="form-select" required <?= $can_collect ? '' : 'disabled' ?>>
            <option value="daily">Daily</option><option value="monthly">Monthly</option>
        </select></div>
    <div class="col-md-3"><label class="form-label">Period Start</label>
        <input type="date" name="period_start" id="period_start" class="form-control" required <?= $can_collect ? '' : 'disabled' ?>></div>
    <div class="col-md-3"><label class="form-label">Period End</label>
        <input type="date" name="period_end" id="period_end" class="form-control" required <?= $can_collect ? '' : 'disabled' ?>></div>
    <div class="col-md-3"><label class="form-label">Computed Amount (₱)</label>
        <input type="text" id="computed_amount" name="computed_amount" class="form-control bg-light" readonly></div>
    <div class="col-md-3"><label class="form-label">Amount Paid (₱) *</label>
        <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control" required <?= $can_collect ? '' : 'disabled' ?>></div>
    <?php if ($user_role === 'admin'): ?>
    <div class="col-md-4"><label class="form-label">Collected By (Maningil)</label>
        <select name="collected_by" class="form-select">
            <?php foreach ($collectors as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $c['id'] == session()->get('user_id') ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
            <?php endforeach; ?>
        </select></div>
    <?php endif; ?>
    <div class="col-12">
        <div id="underpayment_warning" class="alert alert-warning d-none">
            <i class="fa-solid fa-triangle-exclamation"></i> Amount paid is less than computed amount. A note is required before saving.
        </div>
    </div>
    <div class="col-12"><label class="form-label">Notes</label>
        <textarea name="notes" id="payment_notes" class="form-control" rows="2" placeholder="Required if underpayment"></textarea></div>
</div>
<?php if ($can_collect): ?>
<div class="mt-4"><button type="submit" class="btn btn-success btn-lg"><i class="fa-solid fa-receipt"></i> Record Payment &amp; Print Resibo</button></div>
<?php endif; ?>
</form></div></div>
<?= $this->endSection() ?>
