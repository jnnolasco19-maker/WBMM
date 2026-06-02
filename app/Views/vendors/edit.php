<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-4">Edit Vendor — <?= esc($vendor['vendor_no']) ?></h1>
<div class="card"><div class="card-body">
<form method="post" action="<?= base_url('vendors/edit/'.$vendor['id']) ?>">
<?= csrf_field() ?>
<div class="row g-3">
    <div class="col-md-4"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control" value="<?= esc(old('first_name',$vendor['first_name'])) ?>" required></div>
    <div class="col-md-4"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control" value="<?= esc(old('last_name',$vendor['last_name'])) ?>" required></div>
    <div class="col-md-4"><label class="form-label">Business Name</label><input type="text" name="business_name" class="form-control" value="<?= esc(old('business_name',$vendor['business_name'])) ?>"></div>
    <div class="col-md-3"><label class="form-label">Contact</label><input type="text" name="contact" class="form-control" value="<?= esc(old('contact',$vendor['contact'])) ?>"></div>
    <div class="col-md-3"><label class="form-label">Type *</label><select name="type" class="form-select" required>
        <?php foreach (['inside','outside','ambulant'] as $t): ?><option value="<?= $t ?>" <?= old('type',$vendor['type'])===$t?'selected':'' ?>><?= ucfirst($t) ?></option><?php endforeach; ?>
    </select></div>
    <div class="col-md-3"><label class="form-label">Status *</label><select name="status" class="form-select">
        <?php foreach (['active','inactive','suspended'] as $st): ?><option value="<?= $st ?>" <?= old('status',$vendor['status'])===$st?'selected':'' ?>><?= ucfirst($st) ?></option><?php endforeach; ?>
    </select></div>
    <div class="col-md-4"><label class="form-label">ID Type</label><select name="id_type" class="form-select">
        <option value="">—</option>
        <?php foreach (["PhilSys","Driver's License","Passport","Voter's ID","Other"] as $idt): ?>
        <option value="<?= $idt ?>" <?= old('id_type',$vendor['id_type'])===$idt?'selected':'' ?>><?= $idt ?></option>
        <?php endforeach; ?>
    </select></div>
    <div class="col-md-4"><label class="form-label">ID Number</label><input type="text" name="id_number" class="form-control" value="<?= esc(old('id_number',$vendor['id_number'])) ?>"></div>
    <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"><?= esc(old('address',$vendor['address'])) ?></textarea></div>
    <div class="col-md-4"><label class="form-label">Barangay Permit No</label><input type="text" name="barangay_permit_no" class="form-control" value="<?= esc(old('barangay_permit_no',$vendor['barangay_permit_no'])) ?>"></div>
    <div class="col-md-4"><label class="form-label">Barangay Permit Issued</label><input type="date" name="barangay_permit_issued" class="form-control" value="<?= esc(old('barangay_permit_issued',$vendor['barangay_permit_issued'])) ?>"></div>
    <div class="col-md-4"><label class="form-label">Barangay Permit Expiry</label><input type="date" name="barangay_permit_expiry" class="form-control" value="<?= esc(old('barangay_permit_expiry',$vendor['barangay_permit_expiry'])) ?>"></div>
</div>
<div class="mt-4"><button class="btn btn-primary">Update</button>
<a href="<?= base_url('vendors/view/'.$vendor['id']) ?>" class="btn btn-outline-secondary">Cancel</a></div>
</form></div></div>
<?= $this->endSection() ?>
