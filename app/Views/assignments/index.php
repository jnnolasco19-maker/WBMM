<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0">Vendor-Stall Assignments</h1>
    <a href="<?= base_url('assignments/create') ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Assignment</a>
</div>
<div class="card"><div class="table-responsive">
<table class="table table-hover table-wbmm mb-0">
<thead><tr><th>Vendor</th><th>Stall</th><th>Section</th><th>Permit</th><th>Expiry</th><th>Assigned</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php foreach ($assignments as $a): ?>
<tr>
    <td><a href="<?= base_url('vendors/view/'.$a['vendor_id']) ?>"><?= esc($a['vendor_name']) ?></a></td>
    <td><?= esc($a['stall_code']) ?></td>
    <td><?= esc($a['section']) ?></td>
    <td><?= esc($a['permit_no'] ?? '—') ?></td>
    <td><?= esc($a['permit_expiry'] ?? '—') ?></td>
    <td><?= esc($a['assigned_date']) ?></td>
    <td><span class="badge bg-<?= $a['status']==='active'?'success':'secondary' ?>"><?= esc($a['status']) ?></span></td>
    <td>
        <?php if ($a['status']==='active' && $user_role==='admin'): ?>
        <form method="post" action="<?= base_url('assignments/terminate/'.$a['id']) ?>" class="d-inline">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-outline-danger" data-confirm="Terminate this assignment? The stall will become vacant.">Terminate</button>
        </form>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?= $this->endSection() ?>
