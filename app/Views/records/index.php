<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Records & Reports</h1>
        <p class="text-muted mb-0">Monitor and export all public market stall collections.</p>
    </div>
    
    <div class="d-flex gap-2">
        <a href="<?= site_url('records/summary') ?>" class="btn btn-outline-primary rounded-pill px-4">
            <i class="fa-solid fa-chart-pie me-2"></i> Summary Ledger
        </a>
        <?php if ($user_role === 'admin'): ?>
            <a href="<?= base_url('records/audit-logs') ?>" class="btn btn-outline-dark rounded-pill px-4">
                <i class="fa-solid fa-list-check me-2"></i> Audit Console
            </a>
        <?php endif; ?>
        <a href="<?= base_url('records/export?' . http_build_query($_GET)) ?>" class="btn btn-gradient-primary rounded-pill px-4">
            <i class="fa-solid fa-file-csv me-2"></i> Export CSV
        </a>
    </div>
</div>

<!-- COMPREHENSIVE FILTER BOX -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form action="<?= base_url('records') ?>" method="get" class="row g-3">
            <!-- Search term -->
            <div class="col-12 col-md-4">
                <label for="search" class="form-label small fw-semibold text-muted">Search Reference/Vendor</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" id="search" name="search" class="form-control border-start-0" placeholder="ARK-xxx or Vendor name..." value="<?= esc($search) ?>">
                </div>
            </div>

            <!-- Vendor select -->
            <div class="col-6 col-md-4">
                <label for="vendor_id" class="form-label small fw-semibold text-muted">Stall Vendor</label>
                <select id="vendor_id" name="vendor_id" class="form-select">
                    <option value="">-- All Vendors --</option>
                    <?php foreach ($vendors as $vendor): ?>
                        <option value="<?= $vendor['id'] ?>" <?= $vendor_id == $vendor['id'] ? 'selected' : '' ?>>
                            <?= esc($vendor['name']) ?> (<?= esc($vendor['stall_number']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Recurrence -->
            <div class="col-6 col-md-4">
                <label for="payment_type" class="form-label small fw-semibold text-muted">Recurrence</label>
                <select id="payment_type" name="payment_type" class="form-select">
                    <option value="">-- All Recurrences --</option>
                    <option value="daily" <?= $payment_type === 'daily' ? 'selected' : '' ?>>Daily Rent</option>
                    <option value="weekly" <?= $payment_type === 'weekly' ? 'selected' : '' ?>>Weekly Lease</option>
                    <option value="monthly" <?= $payment_type === 'monthly' ? 'selected' : '' ?>>Monthly Lease</option>
                </select>
            </div>

            <!-- Collector staff -->
            <div class="col-6 col-md-4">
                <label for="collected_by" class="form-label small fw-semibold text-muted">Collected By</label>
                <select id="collected_by" name="collected_by" class="form-select">
                    <option value="">-- All Collectors --</option>
                    <?php foreach ($collectors as $coll): ?>
                        <option value="<?= $coll['id'] ?>" <?= $collected_by == $coll['id'] ? 'selected' : '' ?>>
                            <?= esc($coll['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Date start -->
            <div class="col-6 col-md-3">
                <label for="date_from" class="form-label small fw-semibold text-muted">Date Logged From</label>
                <input type="date" id="date_from" name="date_from" class="form-control" value="<?= esc($date_from) ?>">
            </div>

            <!-- Date end -->
            <div class="col-6 col-md-3">
                <label for="date_to" class="form-label small fw-semibold text-muted">Date Logged To</label>
                <input type="date" id="date_to" name="date_to" class="form-control" value="<?= esc($date_to) ?>">
            </div>

            <div class="col-6 col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold">Filter</button>
                <a href="<?= base_url('records') ?>" class="btn btn-light border w-100 fw-bold">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- AUDIT SUMMARY SUMMARY CARD -->
<div class="card card-custom bg-light border-0 shadow-none mb-4">
    <div class="card-body p-4 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                <i class="fa-solid fa-coins fa-xl"></i>
            </div>
            <div>
                <small class="text-muted text-uppercase fw-bold d-block mb-0.5" style="letter-spacing: 0.5px; font-size:0.75rem;">Filtered Remittance Total</small>
                <h3 class="fw-bold mb-0 text-success">₱<?= number_format($total_collections_sum, 2) ?></h3>
            </div>
        </div>
        <span class="text-muted small fw-semibold d-none d-md-block">Summary metrics update in real-time based on filter criteria</span>
    </div>
</div>

<!-- LOGGED TRANSACTIONS LIST -->
<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">Reference No</th>
                        <th class="py-3">Vendor / Stall</th>
                        <th class="py-3">Amount</th>
                        <th class="py-3">Type</th>
                        <th class="py-3">Period Covered</th>
                        <th class="py-3">Collected By</th>
                        <th class="px-4 py-3 text-end">Date Logged</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-folder-open fa-3x mb-3 d-block text-muted"></i>
                                No transaction logs found matching active criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td class="px-4 fw-mono text-dark fw-bold" style="font-family: monospace;"><?= esc($payment['reference_no']) ?></td>
                                <td>
                                    <span class="fw-semibold text-dark d-block"><?= esc($payment['vendor_name']) ?></span>
                                    <small class="text-muted"><?= esc($payment['stall_number']) ?></small>
                                </td>
                                <td class="fw-bold text-success">₱<?= number_format((float) $payment['amount'], 2) ?></td>
                                <td><span class="badge text-uppercase bg-info-subtle text-info border border-info border-opacity-25 px-2 py-1" style="font-size:0.7rem;"><?= esc($payment['payment_type']) ?></span></td>
                                <td class="text-muted" style="font-size: 0.85rem;">
                                    <?= date('M d, Y', strtotime($payment['period_start'])) ?> to <?= date('M d, Y', strtotime($payment['period_end'])) ?>
                                </td>
                                <td><span class="small fw-semibold"><?= esc($payment['collector_name']) ?: 'System' ?></span></td>
                                <td class="px-4 text-end text-muted small"><?= date('M d, Y H:i:s', strtotime($payment['created_at'])) ?></td>
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
