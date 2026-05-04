<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title) ?> — WBMM</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <style>body { background-color: #f4f6f8; }</style>
</head>
<body>

<?= view('layouts/navbar', ['user_name' => $user_name, 'user_role' => $user_role]) ?>

<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            <div class="d-flex align-items-center gap-2 mb-3">
                <a href="<?= base_url('stalls') ?>" class="btn btn-outline-secondary btn-sm">← Back</a>
                <h1 class="h4 mb-0">Edit Stall</h1>
            </div>

            <?php $errors = session()->getFlashdata('errors') ?? []; ?>
            <?php if (! empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="<?= base_url('stalls/edit/' . $stall['id']) ?>" method="post" novalidate>
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="stall_number" class="form-label">Stall Number <span class="text-danger">*</span></label>
                            <input type="text" id="stall_number" name="stall_number"
                                   class="form-control <?= isset($errors['stall_number']) ? 'is-invalid' : '' ?>"
                                   value="<?= esc(old('stall_number', $stall['stall_number'])) ?>" maxlength="20" required>
                            <?php if (isset($errors['stall_number'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['stall_number']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" id="location" name="location"
                                   class="form-control"
                                   value="<?= esc(old('location', $stall['location'])) ?>" maxlength="150">
                        </div>

                        <div class="mb-3">
                            <label for="size" class="form-label">Size</label>
                            <input type="text" id="size" name="size"
                                   class="form-control"
                                   value="<?= esc(old('size', $stall['size'])) ?>" maxlength="50">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>">
                                <option value="vacant"   <?= old('status', $stall['status']) === 'vacant'   ? 'selected' : '' ?>>Vacant</option>
                                <option value="occupied" <?= old('status', $stall['status']) === 'occupied' ? 'selected' : '' ?>>Occupied</option>
                            </select>
                            <?php if (isset($errors['status'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['status']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="vendor_id" class="form-label">Assign Vendor (optional)</label>
                            <select id="vendor_id" name="vendor_id" class="form-select <?= isset($errors['vendor_id']) ? 'is-invalid' : '' ?>">
                                <option value="">— Unassigned —</option>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?= esc($vendor['id']) ?>"
                                        <?= old('vendor_id', $stall['vendor_id']) == $vendor['id'] ? 'selected' : '' ?>>
                                        <?= esc($vendor['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['vendor_id'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['vendor_id']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Stall</button>
                            <a href="<?= base_url('stalls') ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmFXFMrWCU3FA0e6bKIHFORSMR9"
        crossorigin="anonymous"></script>
</body>
</html>
