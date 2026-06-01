<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0">Rate Management</h1>
    <a href="<?= base_url('rates/create') ?>" class="btn btn-primary">Set New Rate</a>
</div>
<?php if ($current): ?>
<div class="alert alert-info">
    <strong>Current Active Rate</strong> (effective <?= esc($current['effective_date']) ?>):
    Inside ₱<?= number_format((float)$current['inside_rate_per_sqm'],2) ?>/sqm daily ·
    Outside ₱<?= number_format((float)$current['outside_monthly_rate'],2) ?>/sqm daily ·
    Ambulant ₱<?= number_format((float)$current['ambulant_daily_rate'],2) ?>/day
    <br><small>Example: A 2.5 sqm inside stall pays ₱<?= number_format(2.5*(float)$current['inside_rate_per_sqm'],2) ?>/day (₱<?= number_format(2.5*(float)$current['inside_rate_per_sqm']*30,2) ?>/month) · A 2.5 sqm outside stall pays ₱<?= number_format(2.5*(float)$current['outside_monthly_rate'],2) ?>/day (₱<?= number_format(2.5*(float)$current['outside_monthly_rate']*30,2) ?>/month)</small>
</div>
<?php endif; ?>
<div class="card"><div class="table-responsive">
<table class="table table-wbmm mb-0">
<thead><tr><th>Effective Date</th><th>Inside/sqm Daily</th><th>Outside/sqm Daily</th><th>Ambulant/Day</th><th>Created By</th><th>Payments Used</th></tr></thead>
<tbody>
<?php foreach ($rates as $r): ?>
<tr>
    <td><?= esc($r['effective_date']) ?></td>
    <td>₱<?= number_format((float)$r['inside_rate_per_sqm'],2) ?></td>
    <td>₱<?= number_format((float)$r['outside_monthly_rate'],2) ?></td>
    <td>₱<?= number_format((float)$r['ambulant_daily_rate'],2) ?></td>
    <td><?= esc($r['created_by_name'] ?? '—') ?></td>
    <td><?= (int)$r['payment_count'] ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?= $this->endSection() ?>
