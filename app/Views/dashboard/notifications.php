<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
    </a>
    <h1 class="h3 fw-bold mb-0">System Notifications</h1>
</div>

<div class="row g-4">
    <!-- Overdue Payments Section -->
    <div class="col-12 col-lg-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title fw-bold mb-0 text-danger">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>Overdue Payments
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Active leaseholders with outstanding Arkalaba balances.</p>
                </div>
                <span class="badge bg-danger rounded-pill px-3 py-1.5"><?= count($overdue_vendors) ?> Alerts</span>
            </div>
            
            <div class="card-body p-0 pt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-2.5">Stall Holder</th>
                                <th class="py-2.5">Stall / Section</th>
                                <th class="py-2.5">Last Covered To</th>
                                <th class="px-4 py-2.5 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($overdue_vendors)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-circle-check fa-3x mb-3 text-success"></i>
                                        <p class="mb-0 fw-semibold">All active vendors are fully paid up!</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($overdue_vendors as $vendor): ?>
                                    <tr>
                                        <td class="px-4">
                                            <span class="fw-semibold text-dark d-block"><?= esc($vendor['name']) ?></span>
                                            <small class="text-muted"><?= esc($vendor['contact']) ?: 'No contact' ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1 mb-1 font-monospace">#<?= esc($vendor['stall_number']) ?></span>
                                            <small class="d-block text-muted small"><?= esc($vendor['section']) ?></small>
                                        </td>
                                        <td class="text-danger fw-semibold">
                                            <?= $vendor['last_payment_date'] ? date('M d, Y', strtotime($vendor['last_payment_date'])) : '<em class="text-muted">Never Paid</em>' ?>
                                        </td>
                                        <td class="px-4 text-end">
                                            <a href="<?= site_url('payments/create?vendor_id=' . $vendor['id']) ?>" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold">
                                                Collect
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Permits Section -->
    <div class="col-12 col-lg-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title fw-bold mb-0 text-warning-emphasis">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>Expiring Permits
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Leaseholders whose municipal permits expire within 30 days.</p>
                </div>
                <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5"><?= count($expiring_permits) ?> Alerts</span>
            </div>
            
            <div class="card-body p-0 pt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-2.5">Stall Holder</th>
                                <th class="py-2.5">Stall / Section</th>
                                <th class="py-2.5">Permit Expiry</th>
                                <th class="px-4 py-2.5 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($expiring_permits)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-circle-check fa-3x mb-3 text-success"></i>
                                        <p class="mb-0 fw-semibold">No lease permits expiring within 30 days.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($expiring_permits as $permit): ?>
                                    <?php 
                                    $today = date('Y-m-d');
                                    $diff = strtotime($permit['permit_expiry']) - strtotime($today);
                                    $daysRemaining = round($diff / (60 * 60 * 24));
                                    ?>
                                    <tr>
                                        <td class="px-4">
                                            <span class="fw-semibold text-dark d-block"><?= esc($permit['name']) ?></span>
                                            <small class="text-muted"><?= esc($permit['contact']) ?: 'No contact' ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1 mb-1 font-monospace">#<?= esc($permit['stall_number']) ?></span>
                                            <small class="d-block text-muted small"><?= esc($permit['section']) ?></small>
                                        </td>
                                        <td>
                                            <span class="text-warning-emphasis fw-bold d-block"><?= date('M d, Y', strtotime($permit['permit_expiry'])) ?></span>
                                            <small class="text-muted small d-block"><?= $daysRemaining ?> day(s) remaining</small>
                                        </td>
                                        <td class="px-4 text-end">
                                            <?php if (session()->get('user_role') === 'admin'): ?>
                                                <a href="<?= site_url('vendors/edit/' . $permit['id']) ?>" class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-bold border-warning text-dark">
                                                    Update
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">Admin Only</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
