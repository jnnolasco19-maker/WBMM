<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= base_url('vendors/view/' . $assign['vendor_id']) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Vendor
    </a>
    <h1 class="h3 fw-bold mb-0">Renew Permit / Edit Assignment</h1>
</div>

<div class="row g-4">
    <!-- Read-only Details Info Card -->
    <div class="col-12 col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-light border-0 py-3">
                <h5 class="card-title fw-bold mb-0 text-secondary">
                    <i class="fa-solid fa-circle-info me-2"></i>Assignment Details
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small d-block">Stall Holder</label>
                    <span class="fw-bold text-dark fs-5"><?= esc($assign['vendor_name']) ?></span>
                    <small class="text-muted d-block"><?= esc($assign['vendor_no']) ?> (<?= esc(ucfirst($assign['vendor_type'])) ?>)</small>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="text-muted small d-block">Assigned Stall</label>
                    <span class="fw-bold text-dark fs-5"><?= esc($assign['stall_code']) ?></span>
                    <small class="text-muted d-block"><?= esc($assign['section']) ?> (<?= esc(ucfirst($assign['stall_type'])) ?><?= $assign['sqm'] ? ' · ' . esc($assign['sqm']) . ' sqm' : '' ?>)</small>
                </div>
                <hr>
                <div class="mb-0">
                    <label class="text-muted small d-block">Status</label>
                    <span class="badge bg-success px-3 py-2 rounded-pill mt-1">Active Assignment</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Editable Permit Details Form Card -->
    <div class="col-12 col-md-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light border-0 py-3">
                <h5 class="card-title fw-bold mb-0 text-primary">
                    <i class="fa-solid fa-pen-to-square me-2"></i>Permit Information
                </h5>
            </div>
            <div class="card-body py-4">
                <form method="post" action="<?= base_url('assignments/edit/' . $assign['id']) ?>">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Permit Number</label>
                            <input type="text" name="permit_no" class="form-control px-3" value="<?= esc(old('permit_no', $assign['permit_no'])) ?>" placeholder="e.g. PRM-2026-0001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Permit Issued</label>
                            <input type="date" name="permit_issued" class="form-control" value="<?= esc(old('permit_issued', $assign['permit_issued'])) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Permit Expiry</label>
                            <input type="date" name="permit_expiry" class="form-control" value="<?= esc(old('permit_expiry', $assign['permit_expiry'])) ?>">
                            <small class="text-muted">Notifications will alert if this date falls within 30 days.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Assigned Date *</label>
                            <input type="date" name="assigned_date" class="form-control" value="<?= esc(old('assigned_date', $assign['assigned_date'])) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control px-3" rows="3" placeholder="Add any special conditions or notes..."><?= esc(old('notes', $assign['notes'])) ?></textarea>
                        </div>
                    </div>
                    <div class="mt-4 pt-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                        </button>
                        <a href="<?= base_url('vendors/view/' . $assign['vendor_id']) ?>" class="btn btn-outline-secondary px-4 py-2">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
