<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0">Permit Expiry Report</h1>
    <a href="<?= base_url('records/permits/export?days='.$days) ?>" class="btn btn-outline-success">Export CSV</a>
</div>
<form class="mb-3" method="get">
    <div class="btn-group" role="group">
        <?php foreach ([30,60,90] as $d): ?>
        <input type="radio" class="btn-check" name="days" id="days<?= $d ?>" value="<?= $d ?>" <?= $days===$d?'checked':'' ?> onchange="this.form.submit()">
        <label class="btn btn-outline-warning" for="days<?= $d ?>">Expiring in <?= $d ?> days</label>
        <?php endforeach; ?>
    </div>
</form>
<div class="card"><div class="table-responsive">
<table class="table table-hover table-wbmm mb-0">
<thead><tr><th>Vendor</th><th>Vendor No</th><th>Stall</th><th>Permit No</th><th>Expiry</th><th>Days Left</th></tr></thead>
<tbody>
<?php foreach ($permits as $p): ?>
<tr class="<?= (int)$p['days_remaining']<=7?'table-warning':'' ?>">
    <td><a href="<?= base_url('vendors/view/'.$p['vendor_id']) ?>"><?= esc($p['vendor_name']) ?></a></td>
    <td><?= esc($p['vendor_no']) ?></td>
    <td><?= esc($p['stall_code']) ?></td>
    <td><?= esc($p['permit_no'] ?? '—') ?></td>
    <td><?= esc($p['permit_expiry']) ?></td>
    <td><?= (int)$p['days_remaining'] ?></td>
</tr>
<?php endforeach; ?>
<?php if (empty($permits)): ?><tr><td colspan="6" class="text-center text-muted py-4">No permits expiring in this period.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?= $this->endSection() ?>
