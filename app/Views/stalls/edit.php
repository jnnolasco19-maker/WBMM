<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-4">Edit Stall — <?= esc($stall['stall_code']) ?></h1>
<div class="card"><div class="card-body">
<form method="post" action="<?= base_url('stalls/edit/' . $stall['id']) ?>">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Stall Code *</label>
            <input type="text" name="stall_code" class="form-control" value="<?= esc(old('stall_code', $stall['stall_code'])) ?>" required></div>
        <div class="col-md-4"><label class="form-label">Section *</label>
            <input type="text" name="section" class="form-control" value="<?= esc(old('section', $stall['section'])) ?>" required></div>
        <div class="col-md-4"><label class="form-label">Type *</label>
            <select name="type" id="stall_type" class="form-select" required>
                <?php foreach (['inside','outside','ambulant'] as $t): ?>
                <option value="<?= $t ?>" <?= old('type', $stall['type']) === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                <?php endforeach; ?>
            </select></div>
        <div class="col-md-4" id="sqm_group"><label class="form-label">SQM</label>
            <input type="number" step="0.01" name="sqm" id="sqm" class="form-control" value="<?= esc(old('sqm', $stall['sqm'])) ?>"></div>
        <div class="col-md-4" id="floor_group"><label class="form-label">Floor Level</label>
            <input type="text" name="floor_level" class="form-control" value="<?= esc(old('floor_level', $stall['floor_level'])) ?>"></div>
        <div class="col-md-4"><label class="form-label">Status</label>
            <select name="status" class="form-select">
                <?php foreach (['vacant','occupied','suspended'] as $st): ?>
                <option value="<?= $st ?>" <?= old('status', $stall['status']) === $st ? 'selected' : '' ?> <?= $st === 'occupied' ? 'disabled' : '' ?>><?= ucfirst($st) ?></option>
                <?php endforeach; ?>
            </select></div>
        <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"><?= esc(old('notes', $stall['notes'])) ?></textarea></div>
    </div>
    <div class="mt-4"><button class="btn btn-primary">Update</button>
        <a href="<?= base_url('stalls') ?>" class="btn btn-outline-secondary">Cancel</a></div>
</form></div></div>
<?= $this->endSection() ?>
