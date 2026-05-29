<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="<?= base_url('vendors') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
            <h1 class="h3 fw-bold mb-0">Register Stall Holder</h1>
        </div>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>

        <div class="card card-custom">
            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('vendors/create') ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <div class="row g-4">
                        <!-- Vendor Name -->
                        <div class="col-12">
                            <label for="name" class="form-label fw-semibold text-dark">Vendor Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" placeholder="Full Legal Name" value="<?= esc(old('name')) ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Stall Number -->
                        <div class="col-12 col-md-6">
                            <label for="stall_number" class="form-label fw-semibold text-dark">Stall Number <span class="text-danger">*</span></label>
                            <input type="text" id="stall_number" name="stall_number" class="form-control <?= isset($errors['stall_number']) ? 'is-invalid' : '' ?>" placeholder="e.g. STALL-001" value="<?= esc(old('stall_number')) ?>" required>
                            <?php if (isset($errors['stall_number'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['stall_number']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Section Dropdown -->
                        <div class="col-12 col-md-6">
                            <label for="section" class="form-label fw-semibold text-dark">Market Section <span class="text-danger">*</span></label>
                            <select id="section" name="section" class="form-select <?= isset($errors['section']) ? 'is-invalid' : '' ?>" required>
                                <option value="">-- Choose Section --</option>
                                <option value="Dry Goods" <?= old('section') === 'Dry Goods' ? 'selected' : '' ?>>Dry Goods</option>
                                <option value="Wet Market" <?= old('section') === 'Wet Market' ? 'selected' : '' ?>>Wet Market</option>
                                <option value="Livestock" <?= old('section') === 'Livestock' ? 'selected' : '' ?>>Livestock</option>
                                <option value="Commercial" <?= old('section') === 'Commercial' ? 'selected' : '' ?>>Commercial</option>
                            </select>
                            <?php if (isset($errors['section'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['section']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Contact -->
                        <div class="col-12 col-md-6">
                            <label for="contact" class="form-label fw-semibold text-dark">Contact Number</label>
                            <input type="text" id="contact" name="contact" class="form-control <?= isset($errors['contact']) ? 'is-invalid' : '' ?>" placeholder="09xxxxxxxxx" value="<?= esc(old('contact')) ?>">
                            <?php if (isset($errors['contact'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['contact']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Permit Expiry -->
                        <div class="col-12 col-md-6">
                            <label for="permit_expiry" class="form-label fw-semibold text-dark">Permit Expiration Date <span class="text-danger">*</span></label>
                            <input type="date" id="permit_expiry" name="permit_expiry" class="form-control <?= isset($errors['permit_expiry']) ? 'is-invalid' : '' ?>" value="<?= esc(old('permit_expiry')) ?>" required>
                            <?php if (isset($errors['permit_expiry'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['permit_expiry']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Status Option -->
                        <div class="col-12">
                            <label for="status" class="form-label fw-semibold text-dark">Stall Lease Status <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" required>
                                <option value="active" <?= old('status') !== 'inactive' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                            <?php if (isset($errors['status'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['status']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-5">
                        <button type="submit" class="btn btn-gradient-primary px-4 py-2">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Register Stall
                        </button>
                        <a href="<?= base_url('vendors') ?>" class="btn btn-light border px-4 py-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
