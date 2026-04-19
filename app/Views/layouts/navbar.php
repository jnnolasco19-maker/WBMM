<?php
/**
 * Navbar partial — role-aware Bootstrap 5 navigation.
 *
 * Expected variables (passed via $data from the parent view or controller):
 *   $user_name  string
 *   $user_role  string  'admin' | 'staff'
 */
$currentUri = current_url(true)->getPath(); // e.g. /dashboard
$isActive   = static fn(string $path): string =>
    str_starts_with($currentUri, $path) ? ' active" aria-current="page' : '';
?>
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">

        <!-- Brand -->
        <a class="navbar-brand fw-bold" href="<?= base_url('dashboard') ?>">WBMM</a>

        <!-- Hamburger toggle -->
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <!-- Left nav links -->
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item">
                    <a class="nav-link<?= $isActive('/dashboard') ?>" href="/dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $isActive('/vendors') ?>" href="/vendors">Vendors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $isActive('/stalls') ?>" href="/stalls">Stalls</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $isActive('/records') ?>" href="/records">Records</a>
                </li>
                <?php if ($user_role === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link<?= $isActive('/users') ?>" href="/users">User Management</a>
                </li>
                <?php endif; ?>
            </ul>

            <!-- Right: user info + logout -->
            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small d-none d-md-inline">
                    <?= esc($user_name) ?>
                    <span class="badge bg-secondary ms-1"><?= esc(ucfirst($user_role)) ?></span>
                </span>
                <form action="<?= base_url('logout') ?>" method="post" class="m-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>

    </div>
</nav>
