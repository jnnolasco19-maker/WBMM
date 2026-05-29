<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-4">Register Vendor</h1>
<div class="card"><div class="card-body">
<form method="post" action="<?= base_url('vendors/create') ?>">
<?= csrf_field() ?>
<div class="row g-3">
    <div class="col-md-3"><label class="form-label">Vendor No</label><input type="text" class="form-control" value="<?= esc($next_vendor_no) ?>" readonly></div>
    <div class="col-md-4"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control" value="<?= old('first_name') ?>" required></div>
    <div class="col-md-5"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control" value="<?= old('last_name') ?>" required></div>
    <div class="col-md-6"><label class="form-label">Business Name</label><input type="text" name="business_name" class="form-control" value="<?= old('business_name') ?>"></div>
    <div class="col-md-3"><label class="form-label">Contact</label><input type="text" name="contact" class="form-control" value="<?= old('contact') ?>"></div>
    <div class="col-md-3"><label class="form-label">Type *</label><select name="type" class="form-select" required>
        <?php foreach (['inside','outside','ambulant'] as $t): ?><option value="<?= $t ?>" <?= old('type')===$t?'selected':'' ?>><?= ucfirst($t) ?></option><?php endforeach; ?>
    </select></div>
    <div class="col-md-4"><label class="form-label">ID Type</label><select name="id_type" class="form-select">
        <option value="">—</option>
        <?php foreach (["PhilSys","Driver's License","Passport","Voter's ID","Other"] as $idt): ?>
        <option value="<?= $idt ?>" <?= old('id_type')===$idt?'selected':'' ?>><?= $idt ?></option>
        <?php endforeach; ?>
    </select></div>
    <div class="col-md-4"><label class="form-label">ID Number</label><input type="text" name="id_number" class="form-control" value="<?= old('id_number') ?>"></div>
    <div class="col-md-4"><label class="form-label">Status *</label><select name="status" class="form-select">
        <?php foreach (['active','inactive','suspended'] as $st): ?><option value="<?= $st ?>"><?= ucfirst($st) ?></option><?php endforeach; ?>
    </select></div>
    <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"><?= old('address') ?></textarea></div>
</div>
<div class="mt-4"><button class="btn btn-primary">Register Vendor</button>
<a href="<?= base_url('vendors') ?>" class="btn btn-outline-secondary">Cancel</a></div>
</form></div></div>
<?= $this->endSection() ?>
