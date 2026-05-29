<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
            <h1 class="h3 fw-bold mb-0">Add User Account</h1>
        </div>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>

        <div class="card card-custom">
            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('users/create') ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <div class="row g-4">
                        <!-- Full Name -->
                        <div class="col-12">
                            <label for="name" class="form-label fw-semibold text-dark">User Account Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" placeholder="Full Name" value="<?= esc(old('name')) ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Email -->
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" placeholder="name@wbmm.com" value="<?= esc(old('email')) ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Password -->
                        <div class="col-12 col-md-6">
                            <label for="password" class="form-label fw-semibold text-dark">Account Password <span class="text-danger">*</span></label>
                            <input type="password" id="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" placeholder="At least 6 characters" required>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Role authority -->
                        <div class="col-12 col-md-6">
                            <label for="role" class="form-label fw-semibold text-dark">Role Authority <span class="text-danger">*</span></label>
                            <select id="role" name="role" class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" required>
                                <option value="staff" <?= old('role') === 'staff' ? 'selected' : '' ?>>Staff Personnel</option>
                                <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>System Administrator</option>
                            </select>
                            <?php if (isset($errors['role'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['role']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Active status -->
                        <div class="col-12 col-md-6">
                            <label for="status" class="form-label fw-semibold text-dark">Account Status <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" required>
                                <option value="active" <?= old('status') !== 'inactive' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Deactivated</option>
                            </select>
                            <?php if (isset($errors['status'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['status']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-5">
                        <button type="submit" class="btn btn-gradient-primary px-4 py-2">
                            <i class="fa-solid fa-user-plus me-2"></i> Register Account
                        </button>
                        <a href="<?= base_url('users') ?>" class="btn btn-light border px-4 py-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>