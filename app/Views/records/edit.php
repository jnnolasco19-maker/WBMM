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
                <a href="<?= base_url('records') ?>" class="btn btn-outline-secondary btn-sm">← Back</a>
                <h1 class="h4 mb-0">Edit Record</h1>
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
                    <form action="<?= base_url('records/edit/' . $record['id']) ?>" method="post" novalidate>
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="vendor_id" class="form-label">Vendor <span class="text-danger">*</span></label>
                            <select id="vendor_id" name="vendor_id"
                                    class="form-select <?= isset($errors['vendor_id']) ? 'is-invalid' : '' ?>" required>
                                <option value="">— Select Vendor —</option>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?= esc($vendor['id']) ?>"
                                        <?= old('vendor_id', $record['vendor_id']) == $vendor['id'] ? 'selected' : '' ?>>
                                        <?= esc($vendor['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['vendor_id'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['vendor_id']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="stall_id" class="form-label">Stall (optional)</label>
                            <select id="stall_id" name="stall_id"
                                    class="form-select <?= isset($errors['stall_id']) ? 'is-invalid' : '' ?>">
                                <option value="">— None —</option>
                                <?php foreach ($stalls as $stall): ?>
                                    <option value="<?= esc($stall['id']) ?>"
                                        <?= old('stall_id', $record['stall_id']) == $stall['id'] ? 'selected' : '' ?>>
                                        <?= esc($stall['stall_number']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['stall_id'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['stall_id']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select id="type" name="type"
                                    class="form-select <?= isset($errors['type']) ? 'is-invalid' : '' ?>" required>
                                <option value="">— Select Type —</option>
                                <option value="payment"   <?= old('type', $record['type']) === 'payment'   ? 'selected' : '' ?>>Payment</option>
                                <option value="violation" <?= old('type', $record['type']) === 'violation' ? 'selected' : '' ?>>Violation</option>
                                <option value="renewal"   <?= old('type', $record['type']) === 'renewal'   ? 'selected' : '' ?>>Renewal</option>
                                <option value="other"     <?= old('type', $record['type']) === 'other'     ? 'selected' : '' ?>>Other</option>
                            </select>
                            <?php if (isset($errors['type'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['type']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="record_date" class="form-label">Record Date <span class="text-danger">*</span></label>
                            <input type="date" id="record_date" name="record_date"
                                   class="form-control <?= isset($errors['record_date']) ? 'is-invalid' : '' ?>"
                                   value="<?= esc(old('record_date', $record['record_date'])) ?>"
                                   max="<?= date('Y-m-d') ?>" required>
                            <?php if (isset($errors['record_date'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['record_date']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" id="amount" name="amount" step="0.01" min="0"
                                       class="form-control <?= isset($errors['amount']) ? 'is-invalid' : '' ?>"
                                       value="<?= esc(old('amount', $record['amount'])) ?>">
                                <?php if (isset($errors['amount'])): ?>
                                    <div class="invalid-feedback"><?= esc($errors['amount']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description"
                                      class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                                      rows="3" maxlength="1000"><?= esc(old('description', $record['description'])) ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['description']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Record</button>
                            <a href="<?= base_url('records') ?>" class="btn btn-outline-secondary">Cancel</a>
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
