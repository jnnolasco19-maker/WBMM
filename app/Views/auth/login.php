<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Gensan Public Market</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            color: #f8fafc;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        }
        .form-control-custom {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.2s;
        }
        .form-control-custom:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #60a5fa;
            color: #ffffff;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.25);
        }
        .form-control-custom::placeholder {
            color: #94a3b8;
        }
        .btn-login {
            background: linear-gradient(45deg, #2563eb, #7c3aed);
            border: none;
            color: #ffffff;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 12px;
            transition: opacity 0.2s;
        }
        .btn-login:hover {
            opacity: 0.9;
            color: #ffffff;
        }
        .logo-container {
            font-size: 3rem;
            background: linear-gradient(45deg, #60a5fa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="card login-card p-4 p-md-5">
        <div class="card-body p-0">
            
            <div class="text-center mb-5">
                <div class="logo-container mb-3">
                    <i class="fa-solid fa-store"></i>
                </div>
                <h1 class="h3 fw-bold mb-1">Gensan Public Market</h1>
                <p class="text-muted small">Web-Based Market Management System</p>
            </div>

            <!-- Error alerts -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-4 py-3 mb-4" role="alert">
                    <div class="d-flex gap-2 align-items-center">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <small class="fw-semibold"><?= esc(session()->getFlashdata('error')) ?></small>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('message')): ?>
                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-4 py-3 mb-4" role="alert">
                    <div class="d-flex gap-2 align-items-center">
                        <i class="fa-solid fa-circle-check"></i>
                        <small class="fw-semibold"><?= esc(session()->getFlashdata('message')) ?></small>
                    </div>
                </div>
            <?php endif; ?>

            <?php $errors = session()->getFlashdata('errors') ?? []; ?>

            <form action="<?= base_url('login') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-4">
                    <label for="email" class="form-label small fw-semibold text-muted">Email Address</label>
                    <div class="position-relative">
                        <input type="email" id="email" name="email" class="form-control form-control-custom <?= isset($errors['email']) ? 'is-invalid' : '' ?>" placeholder="name@wbmm.com" value="<?= esc(old('email')) ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback small"><?= esc($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="password" class="form-label small fw-semibold text-muted mb-0">Password</label>
                    </div>
                    <div class="position-relative">
                        <input type="password" id="password" name="password" class="form-control form-control-custom <?= isset($errors['password']) ? 'is-invalid' : '' ?>" placeholder="••••••••" required>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback small"><?= esc($errors['password']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100 mb-3 shadow-sm">
                    <span>Sign In</span>
                    <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
                </button>
            </form>

        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
