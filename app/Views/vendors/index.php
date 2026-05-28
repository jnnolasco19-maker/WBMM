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

    <!-- Flash messages -->
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
        <h1 class="h3 mb-0">Vendors</h1>
        <?php if ($user_role === 'admin'): ?>
            <a href="<?= base_url('vendors/create') ?>" class="btn btn-primary btn-sm">+ Add Vendor</a>
        <?php endif; ?>
    </div>

    <!-- Search / filter form -->
    <form method="get" action="<?= base_url('vendors') ?>" class="row g-2 mb-3">
        <div class="col-12 col-md-5">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Search name, email, contact…"
                   value="<?= esc($search) ?>">
        </div>
        <div class="col-6 col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="active"   <?= $status === 'active'   ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            <a href="<?= base_url('vendors') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Status</th>
                    <?php if ($user_role === 'admin'): ?>
                        <th class="text-center">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vendors)): ?>
                    <tr>
                        <td colspan="<?= $user_role === 'admin' ? 6 : 5 ?>" class="text-center text-muted py-4">
                            No vendors found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($vendors as $vendor): ?>
                        <tr>
                            <td><?= esc($vendor['id']) ?></td>
                            <td><?= esc($vendor['name']) ?></td>
                            <td><?= esc($vendor['contact_number'] ?? '—') ?></td>
                            <td><?= esc($vendor['email'] ?? '—') ?></td>
                            <td>
                                <span class="badge <?= $vendor['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= esc(ucfirst($vendor['status'])) ?>
                                </span>
                            </td>
                            <?php if ($user_role === 'admin'): ?>
                                <td class="text-center">
                                    <a href="<?= base_url('vendors/edit/' . $vendor['id']) ?>"
                                       class="btn btn-warning btn-sm">Edit</a>
                                    <form action="<?= base_url('vendors/delete/' . $vendor['id']) ?>"
                                          method="post" class="d-inline"
                                          onsubmit="return confirm('Delete this vendor?')">
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

    <!-- Pagination -->
    <?php if ($pager): ?>
        <div class="d-flex justify-content-center">
            <?= $pager->links('vendors', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmFXFMrWCU3FA0e6bKIHFORSMR9"
        crossorigin="anonymous"></script>
</body>
</html>
