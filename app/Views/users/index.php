<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">User Management</h1>
        <p class="text-muted mb-0">List, modify, and control system staff accounts.</p>
    </div>
    <a href="<?= base_url('users/create') ?>" class="btn btn-gradient-primary rounded-pill px-4">
        <i class="fa-solid fa-user-plus me-2"></i> Add Account
    </a>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">User Profile Name</th>
                        <th class="py-3">Email Address</th>
                        <th class="py-3">Role Authority</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Date Registered</th>
                        <th class="px-4 py-3 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="px-4 fw-semibold text-dark"><?= esc($u['name']) ?></td>
                            <td><?= esc($u['email']) ?></td>
                            <td>
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 px-2.5 py-1.5 text-uppercase" style="font-size:0.7rem;">Administrator</span>
                                <?php else: ?>
                                    <span class="badge bg-info-subtle text-info border border-info border-opacity-25 px-2.5 py-1.5 text-uppercase" style="font-size:0.7rem;">Staff Personnel</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($u['status'] === 'active'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-2 py-1.5 rounded-pill text-uppercase" style="font-size:0.7rem;">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 px-2 py-1.5 rounded-pill text-uppercase" style="font-size:0.7rem;">Deactivated</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                            <td class="px-4 text-end">
                                <a href="<?= base_url('users/edit/' . $u['id']) ?>" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1.5 rounded-pill px-3">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                    <span>Edit</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
