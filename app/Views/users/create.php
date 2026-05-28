<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title) ?> — WBMM</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<?= view('layouts/navbar', [
    'user_name' => $user_name,
    'user_role' => $user_role
]) ?>

<main class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <!-- Error Messages -->
            <?php if (!empty(session()->getFlashdata('errors'))): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h1 class="h4 mb-0 py-2">Create New User</h1>
                </div>

                <div class="card-body">
                    <form action="<?= base_url('users/create') ?>" method="post">
                        <?= csrf_field() ?>

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name"
                                   value="<?= esc(old('name')) ?>" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email"
                                   value="<?= esc(old('email')) ?>" required>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>

                        <!-- Role -->
                        <div class="mb-4">
                            <label class="form-label">Role</label>

                            <select class="form-select" name="role" required>
                                <option value="">-- Select Role --</option>

                                <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>
                                    Admin
                                </option>

                                <option value="vendor" <?= old('role') === 'vendor' ? 'selected' : '' ?>>
                                    Vendor
                                </option>

                                <option value="customer" <?= old('role') === 'customer' ? 'selected' : '' ?>>
                                    Customer
                                </option>

                                <option value="cashier" <?= old('role') === 'cashier' ? 'selected' : '' ?>>
                                    Cashier
                                </option>

                                <option value="staff" <?= old('role') === 'staff' ? 'selected' : '' ?>>
                                    Staff
                                </option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">
                                Cancel
                            </a>

                            <button type="submit" class="btn btn-primary">
                                Create User
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>