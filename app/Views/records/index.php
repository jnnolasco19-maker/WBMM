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

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('message')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Records</h1>
        <a href="<?= base_url('records/create') ?>" class="btn btn-primary btn-sm">+ Add Record</a>
    </div>

    <!-- Filters -->
    <form method="get" action="<?= base_url('records') ?>" class="row g-2 mb-3">
        <div class="col-12 col-md-3">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Search vendor, description…"
                   value="<?= esc($search) ?>">
        </div>
        <div class="col-6 col-md-2">
            <select name="type" class="form-select form-select-sm">
                <option value="">All Types</option>
                <option value="payment"   <?= $type === 'payment'   ? 'selected' : '' ?>>Payment</option>
                <option value="violation" <?= $type === 'violation' ? 'selected' : '' ?>>Violation</option>
                <option value="renewal"   <?= $type === 'renewal'   ? 'selected' : '' ?>>Renewal</option>
                <option value="other"     <?= $type === 'other'     ? 'selected' : '' ?>>Other</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <input type="date" name="date_from" class="form-control form-control-sm"
                   value="<?= esc($date_from) ?>" placeholder="From">
        </div>
        <div class="col-6 col-md-2">
            <input type="date" name="date_to" class="form-control form-control-sm"
                   value="<?= esc($date_to) ?>" placeholder="To">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            <a href="<?= base_url('records') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Vendor</th>
                    <th>Stall</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <?php if ($user_role === 'admin'): ?>
                        <th class="text-center">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="<?= $user_role === 'admin' ? 8 : 7 ?>" class="text-center text-muted py-4">
                            No records found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?= esc($record['id']) ?></td>
                            <td><?= esc($record['record_date']) ?></td>
                            <td><?= esc($record['vendor_name'] ?? '—') ?></td>
                            <td><?= esc($record['stall_number'] ?? 'N/A') ?></td>
                            <td>
                                <?php
                                $typeBadge = match($record['type']) {
                                    'payment'   => 'bg-success',
                                    'violation' => 'bg-danger',
                                    'renewal'   => 'bg-primary',
                                    default     => 'bg-secondary',
                                };
                                ?>
                                <span class="badge <?= $typeBadge ?>">
                                    <?= esc(ucfirst($record['type'])) ?>
                                </span>
                            </td>
                            <td><?= $record['amount'] !== null ? '₱' . number_format((float)$record['amount'], 2) : '—' ?></td>
                            <td class="text-truncate" style="max-width:200px;"><?= esc($record['description'] ?? '—') ?></td>
                            <?php if ($user_role === 'admin'): ?>
                                <td class="text-center">
                                    <a href="<?= base_url('records/edit/' . $record['id']) ?>"
                                       class="btn btn-warning btn-sm">Edit</a>
                                    <form action="<?= base_url('records/delete/' . $record['id']) ?>"
                                          method="post" class="d-inline"
                                          onsubmit="return confirm('Delete this record?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pager): ?>
        <div class="d-flex justify-content-center">
            <?= $pager->links('records', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmFXFMrWCU3FA0e6bKIHFORSMR9"
        crossorigin="anonymous"></script>
</body>
</html>
