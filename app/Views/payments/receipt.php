<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<style>
    /* Printing-only style overrides */
    @media print {
        body * {
            visibility: hidden;
        }
        .printable-receipt, .printable-receipt * {
            visibility: visible;
        }
        .printable-receipt {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
        .no-print {
            display: none !important;
        }
        .sidebar, nav, footer {
            display: none !important;
        }
        .content-area {
            padding: 0 !important;
            margin: 0 !important;
        }
    }
    .receipt-header {
        border-bottom: 2px dashed #e2e8f0;
    }
    .receipt-footer {
        border-top: 2px dashed #e2e8f0;
    }
    .receipt-brand {
        font-weight: 800;
        letter-spacing: 1px;
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 no-print">
    <div>
        <h1 class="h3 fw-bold mb-1">Receipt E-Ticket</h1>
        <p class="text-muted mb-0">Official payment collection voucher for GenSan Public Market.</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print();" class="btn btn-gradient-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-print me-2"></i> Print Ticket
        </button>
        <a href="<?= site_url('payments/receipt/' . $payment['id'] . '/pdf') ?>" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-file-pdf me-2"></i> Download as PDF
        </a>
        <a href="<?= base_url('payments') ?>" class="btn btn-outline-secondary rounded-pill px-4">Back</a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        
        <!-- PRINTABLE TICKET CARD -->
        <div class="card card-custom printable-receipt border border-secondary border-opacity-10 p-4 shadow-sm">
            <div class="card-body">
                
                <!-- Receipt Header -->
                <div class="text-center receipt-header pb-4 mb-4">
                    <div class="text-primary h1 mb-2">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <h4 class="receipt-brand text-uppercase mb-1">General Santos City</h4>
                    <h5 class="fw-semibold text-muted small mb-3">PUBLIC MARKET ADMINISTRATION</h5>
                    <p class="text-secondary small mb-0">Arkalaba Stall Rental Receipt</p>
                </div>

                <!-- Transaction Reference -->
                <div class="text-center mb-4">
                    <small class="text-muted text-uppercase d-block mb-1" style="letter-spacing: 0.5px;">Reference Number</small>
                    <h4 class="fw-bold text-dark font-monospace"><?= esc($payment['reference_no']) ?></h4>
                </div>

                <!-- Details List -->
                <div class="row g-3 small mb-4 text-secondary">
                    <div class="col-5">Vendor Leaseholder:</div>
                    <div class="col-7 text-end fw-bold text-dark"><?= esc($payment['vendor_name']) ?></div>

                    <div class="col-5">Assigned Stall:</div>
                    <div class="col-7 text-end fw-bold text-dark font-monospace"><?= esc($payment['stall_number']) ?></div>

                    <div class="col-5">Stall Section:</div>
                    <div class="col-7 text-end fw-bold text-dark"><?= esc($payment['section']) ?></div>

                    <div class="col-5">Collection Type:</div>
                    <div class="col-7 text-end fw-bold text-dark text-uppercase"><?= esc($payment['payment_type']) ?> Rent</div>

                    <div class="col-5 border-top pt-2">Period Covered:</div>
                    <div class="col-7 text-end fw-bold text-dark border-top pt-2">
                        <?= date('M d, Y', strtotime($payment['period_start'])) ?> to <?= date('M d, Y', strtotime($payment['period_end'])) ?>
                    </div>

                    <div class="col-5 border-top pt-2">Collected By:</div>
                    <div class="col-7 text-end fw-bold text-dark border-top pt-2"><?= esc($payment['collector_name']) ?: 'System' ?></div>

                    <div class="col-5">Date & Time Logged:</div>
                    <div class="col-7 text-end fw-bold text-dark"><?= date('M d, Y H:i:s', strtotime($payment['created_at'])) ?></div>
                </div>

                <!-- Amount Statement -->
                <div class="bg-light border rounded-3 p-3 text-center mb-4">
                    <span class="text-muted text-uppercase small d-block mb-1 fw-semibold" style="letter-spacing: 0.5px;">Total Amount Paid</span>
                    <span class="display-6 fw-bold text-success">₱<?= number_format((float) $payment['amount'], 2) ?></span>
                </div>

                <!-- Notes remarks -->
                <?php if (! empty($payment['notes'])): ?>
                    <div class="mb-4 text-start bg-light p-3 rounded border border-secondary border-opacity-10">
                        <small class="text-muted fw-bold d-block mb-1">REMARKS</small>
                        <p class="small text-secondary mb-0"><?= esc($payment['notes']) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Receipt Footer -->
                <div class="text-center receipt-footer pt-4 text-muted small">
                    <p class="mb-1 fw-semibold">Thank you for your prompt daily payment!</p>
                    <p class="mb-0 text-muted" style="font-size: 0.75rem;">This serves as an official electronic e-ticket log for your public market lease.</p>
                </div>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
