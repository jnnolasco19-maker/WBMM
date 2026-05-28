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
        <h1 class="h3 mb-0">Stalls</h1>
        <?php if ($user_role === 'admin'): ?>
            <a href="<?= base_url('stalls/create') ?>" class="btn btn-primary btn-sm">+ Add Stall</a>
        <?php endif; ?>
    </div>

    <form method="get" action="<?= base_url('stalls') ?>" class="row g-2 mb-3">
        <div class="col-12 col-md-5">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Search stall number, location…"
                   value="<?= esc($search) ?>">
        </div>
        <div class="col-6 col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="occupied" <?= $status === 'occupied' ? 'selected' : '' ?>>Occupied</option>
                <option value="vacant"   <?= $status === 'vacant'   ? 'selected' : '' ?>>Vacant</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            <a href="<?= base_url('stalls') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Stall Number</th>
                    <th>Location</th>
                    <th>Size</th>
                    <th>Status</th>
                    <th>Assigned Vendor</th>
                    <?php if ($user_role === 'admin'): ?>
                        <th class="text-center">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($stalls)): ?>
                    <tr>
                        <td colspan="<?= $user_role === 'admin' ? 7 : 6 ?>" class="text-center text-muted py-4">
                            No stalls found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($stalls as $stall): ?>
                        <tr>
                            <td><?= esc($stall['id']) ?></td>
                            <td><?= esc($stall['stall_number']) ?></td>
                            <td><?= esc($stall['location'] ?? '—') ?></td>
                            <td><?= esc($stall['size'] ?? '—') ?></td>
                            <td>
                                <span class="badge <?= $stall['status'] === 'occupied' ? 'bg-danger' : 'bg-success' ?>">
                                    <?= esc(ucfirst($stall['status'])) ?>
                                </span>
                            </td>
                            <td><?= esc($stall['vendor_name'] ?? 'Unassigned') ?></td>
                            <?php if ($user_role === 'admin'): ?>
                                <td class="text-center">
                                    <a href="<?= base_url('stalls/edit/' . $stall['id']) ?>"
                                       class="btn btn-warning btn-sm">Edit</a>
                                    <form action="<?= base_url('stalls/delete/' . $stall['id']) ?>"
                                          method="post" class="d-inline"
                                          onsubmit="return confirm('Delete this stall?')">
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
            <?= $pager->links('stalls', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmFXFMrWCU3FA0e6bKIHFORSMR9"
        crossorigin="anonymous"></script>
</body>
</html>
