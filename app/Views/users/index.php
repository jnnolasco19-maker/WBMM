<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0">User Management</h1>
    <a href="<?= base_url('users/create') ?>" class="btn btn-primary">Add User</a>
</div>
<div class="card"><div class="table-responsive">
<table class="table table-hover table-wbmm mb-0">
<thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php foreach ($users as $u): ?>
<tr>
    <td><?= esc($u['name']) ?></td>
    <td><?= esc($u['email']) ?></td>
    <td><span class="badge bg-primary"><?= esc($u['role']) ?></span></td>
    <td><span class="badge bg-<?= $u['status']==='active'?'success':'secondary' ?>"><?= esc($u['status']) ?></span></td>
    <td class="text-end">
        <?php if ((int)$u['id'] !== (int)session()->get('user_id')): ?>
        <a href="<?= base_url('users/edit/'.$u['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
        <?php if ($u['status']==='active'): ?>
        <form method="post" action="<?= base_url('users/deactivate/'.$u['id']) ?>" class="d-inline">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-outline-danger" data-confirm="Deactivate this user?">Deactivate</button>
        </form>
        <?php endif; ?>
        <?php else: ?><span class="text-muted small">Current user</span><?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?= $this->endSection() ?>
