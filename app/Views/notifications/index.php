<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1 class="h3 mb-4">Notifications & Alerts</h1>

<?php if ($show_overdue ?? true): ?>
<div class="card mb-4 border-danger">
    <div class="card-header bg-danger text-white">Overdue Payments</div>
    <div class="list-group list-group-flush">
        <?php foreach ($overdue as $o): ?>
        <a href="<?= base_url('vendors/view/' . $o['vendor_id']) ?>" class="list-group-item list-group-item-action">
            <strong><?= esc($o['vendor_name']) ?></strong> — <?= esc($o['stall_code'] ?? 'Ambulant') ?>
            <span class="badge bg-danger float-end"><?= (int) $o['days_overdue'] ?> days overdue</span>
        </a>
        <?php endforeach; ?>
        <?php if (empty($overdue)): ?>
        <div class="list-group-item text-muted">No overdue accounts.</div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<div class="card mb-4 border-warning">
    <div class="card-header bg-warning">Expiring Permits (30 days)</div>
    <div class="list-group list-group-flush">
        <?php foreach ($expiring_permits as $p): ?>
        <a href="<?= base_url('vendors/view/' . $p['vendor_id']) ?>" class="list-group-item list-group-item-action">
            <?= esc($p['vendor_name']) ?> — <?= esc($p['stall_code']) ?>
            <span class="float-end text-muted">Expires <?= esc($p['permit_expiry']) ?> (<?= (int) $p['days_remaining'] ?> days)</span>
        </a>
        <?php endforeach; ?>
        <?php if (empty($expiring_permits)): ?>
        <div class="list-group-item text-muted">No permits expiring soon.</div>
        <?php endif; ?>
    </div>
</div>

<?php if ($show_vacant ?? true): ?>
<div class="card border-info">
    <div class="card-header bg-info text-white">Vacant Stalls (<?= (int) $vacant_count ?>)</div>
    <div class="list-group list-group-flush">
        <?php foreach (array_slice($vacant_stalls, 0, 15) as $s): ?>
        <a href="<?= base_url('stalls/view/' . $s['id']) ?>" class="list-group-item list-group-item-action">
            <?= esc($s['stall_code']) ?> — <?= esc($s['section']) ?> (<?= esc($s['type']) ?>)
        </a>
        <?php endforeach; ?>
        <a href="<?= base_url('records/vacant') ?>" class="list-group-item list-group-item-action text-primary fw-semibold">View full vacant stalls report →</a>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
