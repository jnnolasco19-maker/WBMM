<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-4">New Vendor-Stall Assignment</h1>
<div class="card"><div class="card-body">
<form method="post" action="<?= base_url('assignments/create') ?>">
<?= csrf_field() ?>
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Vendor *</label>
        <select name="vendor_id" id="assignment_vendor_id" class="form-select" required>
            <option value="">— Select vendor —</option>
            <?php foreach ($vendors as $v): ?>
            <option value="<?= $v['id'] ?>" data-type="<?= esc($v['type']) ?>" <?= ($pre_vendor && $pre_vendor['id'] == $v['id']) ? 'selected' : '' ?>>
                <?= esc($v['vendor_no'].' — '.$v['first_name'].' '.$v['last_name'].' ('.$v['type'].')') ?>
            </option>
            <?php endforeach; ?>
        </select></div>
    <div class="col-md-6"><label class="form-label">Vacant Stall *</label>
        <select name="stall_id" id="assignment_stall_id" class="form-select" required>
            <option value="">— Select stall —</option>
            <?php foreach ($stalls as $s): ?>
            <option value="<?= $s['id'] ?>" data-type="<?= esc($s['type']) ?>"><?= esc($s['stall_code'].' — '.$s['section'].' ('.$s['type'].')') ?></option>
            <?php endforeach; ?>
        </select>
        <small class="text-muted">Only vacant stalls matching the vendor type are shown.</small></div>
    <div class="col-md-4"><label class="form-label">Permit No</label><input type="text" name="permit_no" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Permit Issued</label><input type="date" name="permit_issued" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Permit Expiry</label><input type="date" name="permit_expiry" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Assigned Date *</label><input type="date" name="assigned_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
    <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
</div>
<div class="mt-4"><button class="btn btn-primary">Assign Stall</button>
<a href="<?= base_url('assignments') ?>" class="btn btn-outline-secondary">Cancel</a></div>
</form></div></div>
<?= $this->endSection() ?>
