<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-6">

        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
            <h1 class="h3 fw-bold mb-0">My Profile</h1>
        </div>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>

        <div class="card card-custom">
            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('profile') ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <div class="row g-4">
                        <div class="col-12">
                            <label for="name" class="form-label fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                                   value="<?= esc(old('name', $user['name'])) ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12">
                            <label for="email" class="form-label fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email"
                                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                   value="<?= esc(old('email', $user['email'])) ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12">
                            <label for="password" class="form-label fw-semibold text-dark">
                                New Password
                                <small class="text-muted fw-normal">(leave blank to keep current)</small>
                            </label>
                            <input type="password" id="password" name="password"
                                   class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                   autocomplete="new-password">
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-5">
                        <button type="submit" class="btn btn-gradient-primary px-4 py-2">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Update Profile
                        </button>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-light border px-4 py-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
