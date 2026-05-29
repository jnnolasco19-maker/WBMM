<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-4">Add Stall</h1>
<div class="card"><div class="card-body">
<form method="post" action="<?= base_url('stalls/create') ?>">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Stall Code *</label>
            <input type="text" name="stall_code" class="form-control" value="<?= old('stall_code') ?>" required></div>
        <div class="col-md-4"><label class="form-label">Section *</label>
            <input type="text" name="section" class="form-control" list="sections" value="<?= old('section') ?>" required>
            <datalist id="sections"><option>Dry Goods</option><option>Wet Market</option><option>Livestock</option><option>Commercial</option><option>Outside Row A</option><option>Outside Row B</option><option>Ambulant</option></datalist></div>
        <div class="col-md-4"><label class="form-label">Type *</label>
            <select name="type" id="stall_type" class="form-select" required>
                <?php foreach (['inside','outside','ambulant'] as $t): ?>
                <option value="<?= $t ?>" <?= old('type') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                <?php endforeach; ?>
            </select></div>
        <div class="col-md-4" id="sqm_group"><label class="form-label">SQM *</label>
            <input type="number" step="0.01" name="sqm" id="sqm" class="form-control" value="<?= old('sqm') ?>"></div>
        <div class="col-md-4" id="floor_group"><label class="form-label">Floor Level</label>
            <input type="text" name="floor_level" class="form-control" value="<?= old('floor_level', 'Ground Floor') ?>"></div>
        <div class="col-md-4"><label class="form-label">Status *</label>
            <select name="status" class="form-select"><option value="vacant">Vacant</option><option value="suspended">Suspended</option></select>
            <small class="text-muted">Occupied is set automatically when a vendor is assigned.</small></div>
        <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"><?= old('notes') ?></textarea></div>
    </div>
    <div class="mt-4"><button class="btn btn-primary">Save Stall</button>
        <a href="<?= base_url('stalls') ?>" class="btn btn-outline-secondary">Cancel</a></div>
</form></div></div>
<?= $this->endSection() ?>
