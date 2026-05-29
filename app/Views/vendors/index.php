<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Vendors</h1>
    <?php if (in_array($user_role, ['admin','staff'], true)): ?>
    <a href="<?= base_url('vendors/create') ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Register Vendor</a>
    <?php endif; ?>
</div>
<form class="row g-2 mb-3" method="get">
    <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Name or vendor no..." value="<?= esc($search) ?>"></div>
    <div class="col-md-2"><select name="type" class="form-select"><option value="">All Types</option>
        <?php foreach (['inside','outside','ambulant'] as $t): ?><option value="<?= $t ?>" <?= $type===$t?'selected':'' ?>><?= ucfirst($t) ?></option><?php endforeach; ?>
    </select></div>
    <div class="col-md-2"><select name="status" class="form-select"><option value="">All Status</option>
        <?php foreach (['active','inactive','suspended'] as $st): ?><option value="<?= $st ?>" <?= $status===$st?'selected':'' ?>><?= ucfirst($st) ?></option><?php endforeach; ?>
    </select></div>
    <div class="col-md-2"><button class="btn btn-outline-secondary w-100">Filter</button></div>
</form>
<div class="card"><div class="table-responsive">
<table class="table table-hover table-wbmm mb-0">
<thead><tr><th>Vendor No</th><th>Name</th><th>Business</th><th>Type</th><th>Stalls</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php foreach ($vendors as $v): ?>
<tr>
    <td><?= esc($v['vendor_no']) ?></td>
    <td><a href="<?= base_url('vendors/view/'.$v['id']) ?>"><?= esc($v['first_name'].' '.$v['last_name']) ?></a></td>
    <td><?= esc($v['business_name'] ?? '—') ?></td>
    <td><span class="badge badge-<?= esc($v['type']) ?>"><?= esc($v['type']) ?></span></td>
    <td><?= (int)$v['stall_count'] ?></td>
    <td><?= esc($v['status']) ?></td>
    <td class="text-end">
        <a href="<?= base_url('vendors/view/'.$v['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
        <?php if ($user_role === 'admin'): ?>
        <a href="<?= base_url('vendors/edit/'.$v['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?= $this->endSection() ?>
