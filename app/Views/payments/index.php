<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Arkalaba Rental Payments</h1>
        <p class="text-muted mb-0">Record and monitor daily, weekly, or monthly collections.</p>
    </div>
    <a href="<?= base_url('payments/create') ?>" class="btn btn-gradient-primary rounded-pill px-4">
        <i class="fa-solid fa-cash-register me-2"></i> Collect Payment
    </a>
</div>

<!-- OVERDUE VENDORS CAROUSEL/LIST -->
<div class="card card-custom border-start border-warning border-4 mb-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h5 class="card-title fw-bold text-warning-emphasis mb-0">
            <i class="fa-solid fa-circle-exclamation text-warning me-2 animate-pulse"></i>Overdue Leaseholders
        </h5>
        <p class="text-muted small mb-0">Active vendors who have outstanding balances or whose paid coverage periods have expired.</p>
    </div>
    <div class="card-body px-4 pb-4">
        <?php if (empty($overdue_vendors)): ?>
            <div class="text-center py-3 text-success fw-semibold">
                <i class="fa-solid fa-circle-check me-2"></i>All active vendors have active covered payment leases!
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead>
                        <tr class="table-warning">
                            <th class="px-3">Vendor</th>
                            <th>Stall Number</th>
                            <th>Market Section</th>
                            <th>Last Covered Period End Date</th>
                            <th class="px-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($overdue_vendors as $vendor): ?>
                            <tr>
                                <td class="px-3 fw-semibold"><?= esc($vendor['name']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= esc($vendor['stall_number']) ?></span></td>
                                <td><?= esc($vendor['section']) ?></td>
                                <td class="text-danger fw-semibold">
                                    <?= $vendor['last_payment_date'] ? date('M d, Y', strtotime($vendor['last_payment_date'])) : '<em class="text-muted small">Never Collected</em>' ?>
                                </td>
                                <td class="px-3 text-end">
                                    <a href="<?= base_url('payments/create?vendor_id=' . $vendor['id']) ?>" class="btn btn-warning btn-sm fw-bold">Collect</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- COLLECTION TRANSACTIONS HISTORY -->
<div class="card card-custom">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h5 class="card-title fw-bold mb-0">Collections Log</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">Reference No</th>
                        <th class="py-3">Vendor / Stall</th>
                        <th class="py-3">Amount</th>
                        <th class="py-3">Collection Type</th>
                        <th class="py-3">Period Covered</th>
                        <th class="py-3">Collected By</th>
                        <th class="px-4 py-3 text-end">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-folder-minus fa-3x mb-3 d-block text-muted"></i>
                                No payments have been logged yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td class="px-4 fw-mono text-dark fw-bold" style="font-family: monospace; font-size: 0.95rem;"><?= esc($payment['reference_no']) ?></td>
                                <td>
                                    <span class="fw-semibold text-dark d-block"><?= esc($payment['vendor_name']) ?></span>
                                    <small class="text-muted"><?= esc($payment['stall_number']) ?></small>
                                </td>
                                <td class="fw-bold text-success">₱<?= number_format((float) $payment['amount'], 2) ?></td>
                                <td><span class="badge text-uppercase bg-info-subtle text-info border border-info border-opacity-25 px-2 py-1" style="font-size:0.7rem;"><?= esc($payment['payment_type']) ?></span></td>
                                <td class="text-muted" style="font-size: 0.85rem;">
                                    <?= date('M d, Y', strtotime($payment['period_start'])) ?> to <?= date('M d, Y', strtotime($payment['period_end'])) ?>
                                </td>
                                <td>
                                    <span class="small fw-semibold"><?= esc($payment['collector_name']) ?: 'System' ?></span>
                                </td>
                                <td class="px-4 text-end">
                                    <a href="<?= base_url('payments/receipt/' . $payment['id']) ?>" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1.5 rounded-pill px-3">
                                        <i class="fa-solid fa-print"></i>
                                        <span>Print Receipt</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <?php if (! empty($pager)): ?>
            <div class="d-flex justify-content-center py-4 bg-light border-top">
                <?= $pager->links('payments', 'bootstrap_pagination') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
