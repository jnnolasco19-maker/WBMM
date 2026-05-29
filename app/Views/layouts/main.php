<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? esc($page_title) : 'WBMM' ?> — Gensan Public Market</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-brand-custom {
            font-weight: 700;
            letter-spacing: 0.5px;
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .sidebar {
            background-color: #1e293b;
            color: #f8fafc;
            width: 260px;
            flex-shrink: 0;
            min-height: calc(100vh - 56px);
            transition: all 0.3s;
        }
        .sidebar .nav-link {
            color: #cbd5e1;
            font-weight: 500;
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover {
            color: #ffffff;
            background-color: #334155;
            padding-left: 1.8rem;
        }
        .sidebar .nav-link.active {
            color: #ffffff;
            background: linear-gradient(45deg, #2563eb, #7c3aed);
            border-left: 4px solid #60a5fa;
        }
        .main-wrapper {
            display: flex;
            flex: 1;
            min-height: calc(100vh - 56px);
        }
        .content-area {
            flex: 1;
            padding: 2rem;
            background-color: #f3f4f6;
            overflow-y: auto;
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s, box-shadow 0.2s;
            background-color: #ffffff;
        }
        .card-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
        }
        .btn-gradient-primary {
            background: linear-gradient(45deg, #2563eb, #7c3aed);
            color: white;
            border: none;
            font-weight: 600;
            transition: opacity 0.2s;
        }
        .btn-gradient-primary:hover {
            opacity: 0.9;
            color: white;
        }
        .badge-expired {
            background-color: #ef4444;
            color: white;
            font-weight: 600;
        }
        .badge-active {
            background-color: #10b981;
            color: white;
            font-weight: 600;
        }
        footer {
            background-color: #ffffff;
            border-top: 1px solid #e5e7eb;
            padding: 1rem 0;
            font-size: 0.9rem;
            color: #6b7280;
        }
        @media (max-width: 768px) {
            .content-area {
                padding: 1rem;
            }
        }
    </style>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <!-- TOP HEADER -->
    <nav class="navbar navbar-expand-lg navbar-white bg-white border-bottom sticky-top py-2 shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= base_url('dashboard') ?>">
                <i class="fa-solid fa-store text-primary" style="font-size: 1.4rem;"></i>
                <span class="navbar-brand-custom h4 mb-0">GenSan WBMM</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="topNavbar">
                <!-- Navigation links visible on mobile and tablet only -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 d-lg-none border-top pt-3 mt-3">
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains(current_url(), '/dashboard') ? 'text-primary fw-bold' : 'text-secondary' ?>" href="<?= base_url('dashboard') ?>">
                            <i class="fa-solid fa-chart-line me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains(current_url(), '/vendors') ? 'text-primary fw-bold' : 'text-secondary' ?>" href="<?= base_url('vendors') ?>">
                            <i class="fa-solid fa-users me-2"></i> Vendors Directory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains(current_url(), '/payments') ? 'text-primary fw-bold' : 'text-secondary' ?>" href="<?= base_url('payments') ?>">
                            <i class="fa-solid fa-cash-register me-2"></i> Arkalaba Collection
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains(current_url(), '/records') ? 'text-primary fw-bold' : 'text-secondary' ?>" href="<?= base_url('records') ?>">
                            <i class="fa-solid fa-file-invoice-dollar text-primary me-2" style="color: #8b5cf6 !important;"></i> Records & Reports
                        </a>
                    </li>
                    <?php if ($user_role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= str_contains(current_url(), '/users') ? 'text-primary fw-bold' : 'text-secondary' ?>" href="<?= base_url('users') ?>">
                            <i class="fa-solid fa-user-gear me-2"></i> User Management
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center gap-3 ms-lg-auto pt-3 pt-lg-0 border-top border-lg-none mt-3 mt-lg-0">
                    <div class="text-end">
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Logged in as</small>
                        <span class="fw-semibold text-dark"><?= esc($user_name) ?></span>
                        <span class="badge text-uppercase ms-1 bg-secondary text-white" style="font-size: 0.65rem;"><?= esc($user_role) ?></span>
                    </div>
                    <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2 rounded-pill px-3 ms-auto">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- WRAPPER -->
    <div class="main-wrapper">
        
        <!-- SIDEBAR (desktop/tablet-landscape only) -->
        <aside class="sidebar d-none d-lg-flex flex-column">
            <ul class="nav flex-column py-3 flex-grow-1">
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(current_url(), '/dashboard') ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(current_url(), '/vendors') ? 'active' : '' ?>" href="<?= base_url('vendors') ?>">
                        <i class="fa-solid fa-users"></i>
                        <span>Vendors Directory</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(current_url(), '/payments') ? 'active' : '' ?>" href="<?= base_url('payments') ?>">
                        <i class="fa-solid fa-cash-register"></i>
                        <span>Arkalaba Collection</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(current_url(), '/records') ? 'active' : '' ?>" href="<?= base_url('records') ?>">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span>Records & Reports</span>
                    </a>
                </li>
                <?php if ($user_role === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains(current_url(), '/users') ? 'active' : '' ?>" href="<?= base_url('users') ?>">
                        <i class="fa-solid fa-user-gear"></i>
                        <span>User Management</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <div class="p-3 text-center border-top border-secondary text-muted small">
                <i class="fa-regular fa-clock me-1"></i> <?= date('Y-m-d H:i') ?>
            </div>
        </aside>

        <!-- CONTENT -->
        <main class="content-area">
            
            <!-- Global Flash alerts -->
            <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 px-4 py-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check h5 mb-0 text-success"></i>
                        <div><?= esc(session()->getFlashdata('message')) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 px-4 py-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation h5 mb-0 text-danger"></i>
                        <div><?= esc(session()->getFlashdata('error')) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Child view injection -->
            <?= $this->renderSection('content') ?>

        </main>
    </div>

    <!-- FOOTER -->
    <footer class="text-center bg-white border-top">
        <div class="container-fluid">
            <p class="mb-0">© 2026 General Santos City Public Market. All Rights Reserved. Powered by CodeIgniter 4.</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
