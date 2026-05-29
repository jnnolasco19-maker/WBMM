<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= base_url() ?>">
    <title><?= esc($page_title ?? 'WBMM') ?> — WBMM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/custom.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php
$role = $user_role ?? session()->get('user_role');
$name = $user_name ?? session()->get('user_name');
$alerts = $alert_count ?? 0;
$uri = service('uri');
$path = $uri->getPath();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid px-3">
        <a class="navbar-brand fw-bold" href="<?= base_url($role === 'collector' ? 'payments/create' : 'dashboard') ?>">
            <i class="fa-solid fa-store me-2"></i>WBMM
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="topNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                <li class="nav-item text-white-50 small d-none d-lg-block">General Santos City Public Market</li>
                <li class="nav-item text-white">
                    <span class="small opacity-75">Logged in:</span>
                    <strong><?= esc($name) ?></strong>
                    <span class="badge bg-light text-primary text-uppercase ms-1"><?= esc($role) ?></span>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="d-flex wbmm-wrapper">
    <aside class="wbmm-sidebar d-none d-lg-flex flex-column">
        <ul class="nav flex-column py-3 flex-grow-1">
            <?php if ($role !== 'collector'): ?>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($path, 'dashboard') ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($path, 'notifications') ? 'active' : '' ?>" href="<?= base_url('notifications') ?>">
                    <i class="fa-solid fa-bell"></i> Notifications
                    <?php if ($alerts > 0): ?><span class="badge bg-danger ms-auto"><?= $alerts ?></span><?php endif; ?>
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($path, 'stalls') ? 'active' : '' ?>" href="<?= base_url('stalls') ?>">
                    <i class="fa-solid fa-border-all"></i> Stalls
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($path, 'vendors') ? 'active' : '' ?>" href="<?= base_url('vendors') ?>">
                    <i class="fa-solid fa-users"></i> Vendors
                </a>
            </li>
            <?php if (in_array($role, ['admin', 'staff'], true)): ?>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($path, 'assignments') ? 'active' : '' ?>" href="<?= base_url('assignments') ?>">
                    <i class="fa-solid fa-link"></i> Assignments
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($path, 'payments') ? 'active' : '' ?>" href="<?= base_url($role === 'collector' ? 'payments/create' : 'payments') ?>">
                    <i class="fa-solid fa-receipt"></i> Arkalaba Collection
                </a>
            </li>
            <?php if ($role === 'collector'): ?>
            <li class="nav-item">
                <a class="nav-link <?= $path === 'records' ? 'active' : '' ?>" href="<?= base_url('records') ?>">
                    <i class="fa-solid fa-list"></i> My Collections
                </a>
            </li>
            <?php endif; ?>
            <?php if ($role !== 'collector'): ?>
            <li class="nav-item">
                <span class="nav-link text-muted small text-uppercase pt-3">Records & Reports</span>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $path === 'records' ? 'active' : '' ?>" href="<?= base_url('records') ?>">
                    <i class="fa-solid fa-list"></i> Transactions
                </a>
            </li>
            <?php if (in_array($role, ['admin', 'supervisor'], true)): ?>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($path, 'records/summary') ? 'active' : '' ?>" href="<?= base_url('records/summary') ?>">
                    <i class="fa-solid fa-chart-pie"></i> Summary
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($path, 'records/overdue') ? 'active' : '' ?>" href="<?= base_url('records/overdue') ?>">
                    <i class="fa-solid fa-circle-exclamation text-danger"></i> Overdue Arkalaba
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($path, 'records/vacant') ? 'active' : '' ?>" href="<?= base_url('records/vacant') ?>">
                    <i class="fa-solid fa-door-open"></i> Vacant Stalls
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($path, 'records/permits') ? 'active' : '' ?>" href="<?= base_url('records/permits') ?>">
                    <i class="fa-solid fa-id-card"></i> Permit Expiry
                </a>
            </li>
            <?php if (in_array($role, ['admin', 'supervisor'], true)): ?>
            <li class="nav-item mt-2">
                <a class="nav-link <?= str_contains($path, 'reports/collector') ? 'active' : '' ?>" href="<?= base_url('reports/collector') ?>">
                    <i class="fa-solid fa-hand-holding-dollar"></i> Collector Remittance
                </a>
            </li>
            <?php endif; ?>
            <?php if ($role === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($path, 'rates') ? 'active' : '' ?>" href="<?= base_url('rates') ?>">
                    <i class="fa-solid fa-tags"></i> Rate Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($path, 'users') ? 'active' : '' ?>" href="<?= base_url('users') ?>">
                    <i class="fa-solid fa-user-gear"></i> User Management
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>
        </ul>
        <div class="p-3 border-top border-secondary text-muted small text-center">
            LGU General Santos City
        </div>
    </aside>

    <main class="wbmm-content flex-grow-1">
        <div class="container-fluid py-4">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show"><?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show"><?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show"><?= esc(session()->getFlashdata('warning')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </main>
</div>

<footer class="wbmm-footer text-center py-3 border-top bg-white">
    <small class="text-muted">© <?= date('Y') ?> WBMM — Web-Based Market Management System · General Santos City Public Market</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/wbmm.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
