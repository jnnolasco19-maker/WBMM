<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Market Dashboard</h1>
        <p class="text-muted mb-0">General Santos City Public Market daily yields & metrics.</p>
    </div>
</div>

<!-- SYSTEM ALERTS / NOTIFICATIONS BANNER -->
<?php if (! empty($overdue_vendors) || ! empty($expiring_permits)): ?>
    <div class="row g-3 mb-4">
        <?php if (! empty($overdue_vendors)): ?>
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-3 p-4 mb-0 rounded-3" role="alert">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2.5 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink:0;">
                        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading fw-bold mb-1 text-danger">Overdue Arkalaba Rental Payments Detected</h6>
                        <p class="small mb-0 text-secondary">
                            There are currently <strong><?= count($overdue_vendors) ?></strong> active vendor(s) who have missed rental payments for the current expected billing period.
                        </p>
                    </div>
                    <div class="d-flex gap-2 ms-auto me-4 flex-shrink-0">
                        <a href="<?= site_url('notifications') ?>" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold">
                            <i class="fa-solid fa-bell me-1"></i> View Alerts
                        </a>
                        <a href="<?= site_url('payments') ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                            <i class="fa-solid fa-cash-register me-1"></i> Collect Now
                        </a>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="top: 50%; transform: translateY(-50%);"></button>
                </div>
            </div>
        <?php endif; ?>

        <?php if (! empty($expiring_permits)): ?>
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-3 p-4 mb-0 rounded-3" role="alert">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2.5 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink:0;">
                        <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading fw-bold mb-1 text-warning-emphasis">Stall Lease Permits Expiring Soon</h6>
                        <p class="small mb-0 text-secondary">
                            There are <strong><?= count($expiring_permits) ?></strong> active vendor(s) whose municipal lease permits are set to expire within the next 30 days.
                        </p>
                    </div>
                    <div class="d-flex gap-2 ms-auto me-4 flex-shrink-0">
                        <a href="<?= site_url('notifications') ?>" class="btn btn-warning btn-sm rounded-pill px-3 fw-semibold text-dark">
                            <i class="fa-solid fa-bell me-1"></i> View Alerts
                        </a>
                        <a href="<?= site_url('vendors') ?>?expiring_soon=1" class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-semibold text-dark border-warning">
                            <i class="fa-solid fa-address-card me-1"></i> Audit Permits
                        </a>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="top: 50%; transform: translateY(-50%);"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- STATISTICAL STATUS CARDS -->
<div class="row g-4 mb-4">
    <!-- Total Vendors -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom h-100 border-start border-primary border-4 py-2">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted text-uppercase small fw-bold mb-1">Total Vendors</p>
                        <h2 class="fw-bold mb-0 text-primary"><?= esc($total_vendors) ?></h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="fa-solid fa-users fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Stalls -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom h-100 border-start border-success border-4 py-2">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted text-uppercase small fw-bold mb-1">Active Stalls</p>
                        <h2 class="fw-bold mb-0 text-success"><?= esc($active_stalls) ?></h2>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                        <i class="fa-solid fa-store fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Collections -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom h-100 border-start border-purple border-4 py-2" style="border-color: #8b5cf6 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted text-uppercase small fw-bold mb-1">Collections This Month</p>
                        <h2 class="fw-bold mb-0 text-dark">₱<?= number_format($total_collections_this_month, 2) ?></h2>
                    </div>
                    <div class="bg-purple bg-opacity-10 text-purple rounded-3 p-3" style="background-color: rgba(139, 92, 246, 0.1) !important; color: #8b5cf6 !important;">
                        <i class="fa-solid fa-wallet fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Accounts -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom h-100 border-start border-danger border-4 py-2">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted text-uppercase small fw-bold mb-1">Overdue Accounts</p>
                        <h2 class="fw-bold mb-0 text-danger"><?= esc($overdue_accounts_count) ?></h2>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                        <i class="fa-solid fa-circle-exclamation fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS AND LISTS -->
<div class="row g-4 mb-4">
    <!-- Bar Chart (Collections Past 6 Months) -->
    <div class="col-12 col-xl-7">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0">Collections Trend</h5>
                <span class="badge bg-light text-muted" style="font-size: 0.75rem;">Past 6 Months</span>
            </div>
            <div class="card-body px-4 pb-4">
                <div style="position: relative; height:320px; width:100%">
                    <canvas id="monthlyCollectionsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Actions/Payments (Last 10 Payments) -->
    <div class="col-12 col-xl-5">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0">Recent Payments</h5>
                <a href="<?= base_url('records') ?>" class="small text-decoration-none">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Vendor / Stall</th>
                                <th class="py-3">Amount</th>
                                <th class="px-4 py-3 text-end">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_payments)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No collections recorded today.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_payments as $payment): ?>
                                    <tr>
                                        <td class="px-4">
                                            <span class="fw-semibold text-dark d-block"><?= esc($payment['vendor_name']) ?></span>
                                            <small class="text-muted"><?= esc($payment['stall_number']) ?> (<?= esc($payment['payment_type']) ?>)</small>
                                        </td>
                                        <td class="fw-bold text-success">₱<?= number_format((float) $payment['amount'], 2) ?></td>
                                        <td class="px-4 text-end text-muted"><?= date('M d, Y', strtotime($payment['created_at'])) ?></td>
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

<!-- INLINE CHART RENDERING SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyCollectionsChart').getContext('2d');
    
    const labels = <?= $chart_labels ?>;
    const data   = <?= $chart_data ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Monthly Collections (₱)',
                data: data,
                backgroundColor: 'rgba(59, 130, 246, 0.85)',
                borderColor: '#2563eb',
                borderWidth: 1,
                borderRadius: 6,
                hoverBackgroundColor: 'rgba(139, 92, 246, 0.85)',
                hoverBorderColor: '#7c3aed'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.raw.toLocaleString(undefined, {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    },
                    grid: {
                        borderDash: [5, 5]
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>

<?= $this->endSection() ?>
