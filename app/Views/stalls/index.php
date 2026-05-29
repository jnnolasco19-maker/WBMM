<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Stalls</h1>
    <?php if ($user_role === 'admin'): ?>
    <a href="<?= base_url('stalls/create') ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Stall</a>
    <?php endif; ?>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-2"><select name="type" class="form-select"><option value="">All Types</option>
        <?php foreach (['inside','outside','ambulant'] as $t): ?>
        <option value="<?= $t ?>" <?= $type === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
        <?php endforeach; ?>
    </select></div>
    <div class="col-md-3"><select name="section" class="form-select"><option value="">All Sections</option>
        <?php foreach ($sections as $sec): ?>
        <option value="<?= esc($sec['section']) ?>" <?= $section === $sec['section'] ? 'selected' : '' ?>><?= esc($sec['section']) ?></option>
        <?php endforeach; ?>
    </select></div>
    <div class="col-md-2"><select name="status" class="form-select"><option value="">All Status</option>
        <?php foreach (['vacant','occupied','suspended'] as $st): ?>
        <option value="<?= $st ?>" <?= $status === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
        <?php endforeach; ?>
    </select></div>
    <div class="col-md-3"><input type="text" name="search" class="form-control" placeholder="Search code..." value="<?= esc($search) ?>"></div>
    <div class="col-md-2"><button class="btn btn-outline-secondary w-100">Filter</button></div>
</form>

<div class="card"><div class="table-responsive">
<table class="table table-hover table-wbmm mb-0">
<thead><tr><th>Code</th><th>Section</th><th>Type</th><th>SQM</th><th>Vendor</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php foreach ($stalls as $s): ?>
<tr>
    <td><a href="<?= base_url('stalls/view/' . $s['id']) ?>"><?= esc($s['stall_code']) ?></a></td>
    <td><?= esc($s['section']) ?></td>
    <td><span class="badge badge-<?= esc($s['type']) ?>"><?= esc($s['type']) ?></span></td>
    <td><?= $s['sqm'] ? esc($s['sqm']) : '—' ?></td>
    <td><?= esc($s['vendor_name'] ?? '—') ?></td>
    <td><span class="badge bg-<?= $s['status'] === 'occupied' ? 'success' : ($s['status'] === 'vacant' ? 'secondary' : 'warning') ?>"><?= esc($s['status']) ?></span></td>
    <td class="text-end">
        <a href="<?= base_url('stalls/view/' . $s['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
        <?php if ($user_role === 'admin'): ?>
        <a href="<?= base_url('stalls/edit/' . $s['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
        <?php if ($s['status'] !== 'occupied'): ?>
        <form method="post" action="<?= base_url('stalls/delete/' . $s['id']) ?>" class="d-inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm="Delete this stall?">Delete</button>
        </form>
        <?php endif; ?>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?= $this->endSection() ?>
