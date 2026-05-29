<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('records') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Reports
        </a>
        <h1 class="h3 fw-bold mb-0">Financial Summary Ledger</h1>
    </div>
    <a href="<?= site_url('records/summary/export') ?>" class="btn btn-gradient-primary rounded-pill px-4">
        <i class="fa-solid fa-file-csv me-2"></i> Export Summary CSV
    </a>
</div>

<div class="row g-4">
    <!-- 1. By Collection Type -->
    <div class="col-12 col-lg-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title fw-bold mb-0 text-dark">Collections By Rental Type</h5>
                <p class="text-muted small mb-0 mt-1">Aggregated collections grouped by billing frequency (Daily / Weekly / Monthly).</p>
            </div>
            <div class="card-body p-0 pt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Billing Mode</th>
                                <th class="py-3 text-center">Transaction Count</th>
                                <th class="px-4 py-3 text-end">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalCountPT = 0;
                            $totalSumPT = 0.00;
                            if (empty($by_payment_type)): 
                            ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No records registered.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($by_payment_type as $pt): ?>
                                    <tr>
                                        <td class="px-4 fw-semibold text-dark text-uppercase"><?= esc($pt['payment_type']) ?></td>
                                        <td class="text-center text-muted font-monospace"><?= esc($pt['count']) ?></td>
                                        <td class="px-4 text-end fw-bold text-dark">₱<?= number_format((float) $pt['total'], 2) ?></td>
                                    </tr>
                                    <?php 
                                    $totalCountPT += (int) $pt['count'];
                                    $totalSumPT += (float) $pt['total'];
                                    ?>
                                <?php endforeach; ?>
                                <tr class="table-light border-top border-dark border-opacity-10 fw-bold" style="font-size: 1rem;">
                                    <td class="px-4 text-dark">GRAND TOTAL</td>
                                    <td class="text-center text-dark font-monospace"><?= $totalCountPT ?></td>
                                    <td class="px-4 text-end text-success">₱<?= number_format($totalSumPT, 2) ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. By Market Section -->
    <div class="col-12 col-lg-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title fw-bold mb-0 text-dark">Collections By Stall Section</h5>
                <p class="text-muted small mb-0 mt-1">Aggregated collections grouped by public market administrative zones.</p>
            </div>
            <div class="card-body p-0 pt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Market Section</th>
                                <th class="py-3 text-center">Transaction Count</th>
                                <th class="px-4 py-3 text-end">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalCountSec = 0;
                            $totalSumSec = 0.00;
                            if (empty($by_section)): 
                            ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No records registered.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($by_section as $sec): ?>
                                    <tr>
                                        <td class="px-4 fw-semibold text-dark"><?= esc($sec['section']) ?></td>
                                        <td class="text-center text-muted font-monospace"><?= esc($sec['count']) ?></td>
                                        <td class="px-4 text-end fw-bold text-dark">₱<?= number_format((float) $sec['total'], 2) ?></td>
                                    </tr>
                                    <?php 
                                    $totalCountSec += (int) $sec['count'];
                                    $totalSumSec += (float) $sec['total'];
                                    ?>
                                <?php endforeach; ?>
                                <tr class="table-light border-top border-dark border-opacity-10 fw-bold" style="font-size: 1rem;">
                                    <td class="px-4 text-dark">GRAND TOTAL</td>
                                    <td class="text-center text-dark font-monospace"><?= $totalCountSec ?></td>
                                    <td class="px-4 text-end text-success">₱<?= number_format($totalSumSec, 2) ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Monthly Trends -->
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title fw-bold mb-0 text-dark">Monthly Collection History</h5>
                <p class="text-muted small mb-0 mt-1">Audit of gross municipal collections computed on a calendar month-on-month trend.</p>
            </div>
            <div class="card-body p-0 pt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Calendar Month</th>
                                <th class="py-3 text-center">Transaction Count</th>
                                <th class="px-4 py-3 text-end">Total Collections</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalCountMon = 0;
                            $totalSumMon = 0.00;
                            if (empty($by_month)): 
                            ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No records registered.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($by_month as $mon): ?>
                                    <tr>
                                        <td class="px-4 fw-semibold text-dark"><?= date('F Y', strtotime($mon['month_val'] . '-01')) ?></td>
                                        <td class="text-center text-muted font-monospace"><?= esc($mon['count']) ?></td>
                                        <td class="px-4 text-end fw-bold text-dark">₱<?= number_format((float) $mon['total'], 2) ?></td>
                                    </tr>
                                    <?php 
                                    $totalCountMon += (int) $mon['count'];
                                    $totalSumMon += (float) $mon['total'];
                                    ?>
                                <?php endforeach; ?>
                                <tr class="table-light border-top border-dark border-opacity-10 fw-bold" style="font-size: 1rem;">
                                    <td class="px-4 text-dark">GRAND TOTAL</td>
                                    <td class="text-center text-dark font-monospace"><?= $totalCountMon ?></td>
                                    <td class="px-4 text-end text-success">₱<?= number_format($totalSumMon, 2) ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
