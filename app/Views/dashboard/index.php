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
    <style>
        body { background-color: #f4f6f8; }
        .stat-card { border-left: 4px solid; }
        .stat-card.vendors  { border-color: #0d6efd; }
        .stat-card.stalls   { border-color: #198754; }
        .stat-card.occupied { border-color: #dc3545; }
        .stat-card.vacant   { border-color: #ffc107; }
        .stat-card.records  { border-color: #6f42c1; }
        .stat-value { font-size: 2rem; font-weight: 700; }
    </style>
</head>
<body>

<?= view('layouts/navbar', ['user_name' => $user_name, 'user_role' => $user_role]) ?>

<main class="container py-4">

    <!-- Flash messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('message')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Welcome heading -->
    <div class="d-flex align-items-center gap-2 mb-4">
        <h1 class="h3 mb-0">Welcome, <?= esc($user_name) ?></h1>
        <span class="badge bg-secondary"><?= esc(ucfirst($user_role)) ?></span>
    </div>

    <!-- Stat cards -->
    <div class="row g-3 mb-4">

        <!-- Total Vendors — visible to all roles -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card vendors h-100 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Vendors</p>
                    <p class="stat-value text-primary mb-0">
                        <?= esc($stats['total_vendors']) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Stalls — visible to all roles -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card stalls h-100 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Stalls</p>
                    <p class="stat-value text-success mb-0">
                        <?= esc($stats['total_stalls']) ?>
                    </p>
                </div>
            </div>
        </div>

        <?php if ($user_role === 'admin'): ?>

        <!-- Occupied Stalls -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card occupied h-100 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Occupied Stalls</p>
                    <p class="stat-value text-danger mb-0">
                        <?= esc($stats['occupied_stalls']) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Vacant Stalls -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card vacant h-100 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Vacant Stalls</p>
                    <p class="stat-value text-warning mb-0">
                        <?= esc($stats['vacant_stalls']) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Records -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card records h-100 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Records</p>
                    <p class="stat-value text-purple mb-0" style="color:#6f42c1;">
                        <?= esc($stats['total_records']) ?>
                    </p>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>

    <!-- Management shortcuts -->
    <h2 class="h5 mb-3">Quick Actions</h2>
    <div class="row g-2 mb-4">

        <?php if ($user_role === 'admin'): ?>
            <div class="col-auto">
                <a href="/vendors" class="btn btn-primary">Manage Vendors</a>
            </div>
            <div class="col-auto">
                <a href="/stalls" class="btn btn-success">Manage Stalls</a>
            </div>
            <div class="col-auto">
                <a href="/records" class="btn btn-purple" style="background:#6f42c1;color:#fff;border-color:#6f42c1;">Manage Records</a>
            </div>
        <?php else: ?>
            <div class="col-auto">
                <a href="/vendors" class="btn btn-outline-primary">View Vendors</a>
            </div>
            <div class="col-auto">
                <a href="/stalls" class="btn btn-outline-success">View Stalls</a>
            </div>
        <?php endif; ?>

    </div>

    <!-- Loaded-at timestamp -->
    <p class="text-muted small mt-4">
        Page loaded at: <?= esc($loaded_at) ?>
    </p>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmFXFMrWCU3FA0e6bKIHFORSMR9"
        crossorigin="anonymous"></script>
</body>
</html>
