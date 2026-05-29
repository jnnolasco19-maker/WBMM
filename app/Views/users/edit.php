<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
            <h1 class="h3 fw-bold mb-0">Edit User Account</h1>
        </div>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>

        <!-- Highlight if self modification -->
        <?php 
        $isSelf = ((int) $user['id'] === (int) session()->get('user_id')); 
        ?>

        <div class="card card-custom">
            <div class="card-body p-4 p-md-5">
                
                <?php if ($isSelf): ?>
                    <div class="alert alert-info border-0 rounded-4 px-4 py-3 mb-4" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-info h5 mb-0 text-info"></i>
                            <div class="small fw-semibold">You are currently editing your own logged-in admin account. Self-deactivation and demotion are blocked for system security.</div>
                        </div>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('users/edit/' . $user['id']) ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <div class="row g-4">
                        <!-- Full Name -->
                        <div class="col-12">
                            <label for="name" class="form-label fw-semibold text-dark">User Account Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" placeholder="Full Name" value="<?= esc(old('name', $user['name'])) ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Email -->
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" placeholder="name@wbmm.com" value="<?= esc(old('email', $user['email'])) ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Password -->
                        <div class="col-12 col-md-6">
                            <label for="password" class="form-label fw-semibold text-dark">Password (Leave blank to keep current)</label>
                            <input type="password" id="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" placeholder="••••••••">
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Role authority -->
                        <div class="col-12 col-md-6">
                            <label for="role" class="form-label fw-semibold text-dark">Role Authority <span class="text-danger">*</span></label>
                            <select id="role" name="role" class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" required <?= $isSelf ? 'disabled' : '' ?>>
                                <option value="staff" <?= old('role', $user['role']) === 'staff' ? 'selected' : '' ?>>Staff Personnel</option>
                                <option value="admin" <?= old('role', $user['role']) === 'admin' ? 'selected' : '' ?>>System Administrator</option>
                            </select>
                            <?php if ($isSelf): ?>
                                <!-- Keep value submitted if input is disabled in view -->
                                <input type="hidden" name="role" value="admin">
                            <?php endif; ?>
                            <?php if (isset($errors['role'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['role']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Active status -->
                        <div class="col-12 col-md-6">
                            <label for="status" class="form-label fw-semibold text-dark">Account Status <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" required <?= $isSelf ? 'disabled' : '' ?>>
                                <option value="active" <?= old('status', $user['status']) === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status', $user['status']) === 'inactive' ? 'selected' : '' ?>>Deactivated</option>
                            </select>
                            <?php if ($isSelf): ?>
                                <!-- Keep value submitted if input is disabled in view -->
                                <input type="hidden" name="status" value="active">
                            <?php endif; ?>
                            <?php if (isset($errors['status'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['status']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-5">
                        <button type="submit" class="btn btn-gradient-primary px-4 py-2">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
                        </button>
                        <a href="<?= base_url('users') ?>" class="btn btn-light border px-4 py-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
