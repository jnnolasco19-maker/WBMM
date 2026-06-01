<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-4">Collect Arkalaba</h1>
<?php if (! $can_collect): ?>
<div class="alert alert-info">You have view-only access. Contact a collector or administrator to record payments.</div>
<?php endif; ?>
<div class="card"><div class="card-body">
<form method="post" action="<?= base_url('payments/create') ?>" id="paymentForm">
<?= csrf_field() ?>
<input type="hidden" id="stall_type_val" name="stall_type_hidden" value="">
<input type="hidden" id="sqm_val" value="0">
<input type="hidden" id="rate_used" name="rate_used" value="">
<input type="hidden" id="preselect_stall_id" value="<?= (int) ($query_stall_id ?? 0) ?>">

<?php if ($can_collect): ?>
<div class="d-print-none mb-4">
    <button type="button" class="btn btn-primary btn-lg w-100 py-3 fw-bold" id="startScannerBtn">
        <i class="fa-solid fa-camera me-2"></i> Scan Vendor / Stall QR Code
    </button>
</div>
<?php endif; ?>

<h5 class="text-muted mb-3">Step 1 — Select Vendor</h5>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">Search vendor</label>
        <input type="text" id="vendor_search" class="form-control" placeholder="Name or vendor no..." <?= $can_collect ? '' : 'disabled' ?>>
    </div>
    <div class="col-md-5">
        <label class="form-label">Vendor *</label>
        <select name="vendor_id" id="vendor_id" class="form-select" required <?= $can_collect ? '' : 'disabled' ?>>
            <option value="">— Select vendor —</option>
            <?php foreach ($vendors as $v): ?>
            <option value="<?= $v['id'] ?>" <?= ($query_vendor_id ?? 0) == $v['id'] ? 'selected' : '' ?>>
                <?= esc($v['vendor_no'].' — '.$v['first_name'].' '.$v['last_name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <span id="vendor_type_badge" class="badge bg-secondary mb-2">—</span>
    </div>
</div>

<h5 class="text-muted mb-3">Step 2 — Select Stall</h5>
<div id="stall_section" class="row g-3 mb-4">
    <div class="col-md-8">
        <select name="stall_id" id="stall_id" class="form-select" <?= $can_collect ? '' : 'disabled' ?>>
            <option value="">— Select stall —</option>
        </select>
        <small class="text-muted">Skipped for ambulant vendors (no fixed stall).</small>
    </div>
</div>

<h5 class="text-muted mb-3">Step 3 — Payment Details</h5>
<div class="row g-3">
    <div class="col-md-3"><label class="form-label">Payment Type</label>
        <select name="payment_type" id="payment_type" class="form-select" required <?= $can_collect ? '' : 'disabled' ?>>
            <option value="daily">Daily</option><option value="monthly">Monthly</option>
        </select></div>
    <div class="col-md-3"><label class="form-label">Period Start</label>
        <input type="date" name="period_start" id="period_start" class="form-control" required <?= $can_collect ? '' : 'disabled' ?>></div>
    <div class="col-md-3"><label class="form-label">Period End</label>
        <input type="date" name="period_end" id="period_end" class="form-control" required <?= $can_collect ? '' : 'disabled' ?>></div>
    <div class="col-md-3"><label class="form-label">Computed Amount (₱)</label>
        <input type="text" id="computed_amount" name="computed_amount" class="form-control bg-light" readonly></div>
    <div class="col-md-3"><label class="form-label">Amount Paid (₱) *</label>
        <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control" required <?= $can_collect ? '' : 'disabled' ?>></div>
    <?php if ($user_role === 'admin'): ?>
    <div class="col-md-4"><label class="form-label">Collected By (Maningil)</label>
        <select name="collected_by" class="form-select">
            <?php foreach ($collectors as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $c['id'] == session()->get('user_id') ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
            <?php endforeach; ?>
        </select></div>
    <?php endif; ?>
    <div class="col-12">
        <div id="underpayment_warning" class="alert alert-warning d-none">
            <i class="fa-solid fa-triangle-exclamation"></i> Amount paid is less than computed amount. A note is required before saving.
        </div>
    </div>
    <div class="col-12"><label class="form-label">Notes</label>
        <textarea name="notes" id="payment_notes" class="form-control" rows="2" placeholder="Required if underpayment"></textarea></div>
</div>
<?php if ($can_collect): ?>
<div class="mt-4"><button type="submit" class="btn btn-success btn-lg"><i class="fa-solid fa-receipt"></i> Record Payment &amp; Print Resibo</button></div>
<?php endif; ?>
</form></div></div>

<!-- QR Scanner Modal -->
<div class="modal fade" id="scannerModal" tabindex="-1" aria-labelledby="scannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="scannerModalLabel"><i class="fa-solid fa-qrcode me-2"></i>Scan QR Code</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qr-reader" style="width: 100%; max-width: 400px; margin: 0 auto;"></div>
                <div id="qr-reader-results" class="mt-3 text-muted small">Point your camera at a Stall or Vendor QR Code.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrcodeScanner = null;

    if (document.getElementById('startScannerBtn')) {
        document.getElementById('startScannerBtn').addEventListener('click', function() {
            const scannerModal = new bootstrap.Modal(document.getElementById('scannerModal'));
            scannerModal.show();
        });
    }

    document.getElementById('scannerModal').addEventListener('shown.bs.modal', function () {
        html5QrcodeScanner = new Html5Qrcode("qr-reader");
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            // Stop scanning and turn off camera
            html5QrcodeScanner.stop().then((ignore) => {
                try {
                    // Instantly redirect to the pre-filled payment creation page!
                    if (decodedText.includes('payments/create')) {
                        window.location.href = decodedText;
                    } else {
                        alert('Invalid QR Code. Please scan a valid WBMM Stall or Vendor tag.');
                        startScanning();
                    }
                } catch (e) {
                    alert('Invalid QR Code structure.');
                    startScanning();
                }
            });
            
            // Hide modal
            bootstrap.Modal.getInstance(document.getElementById('scannerModal')).hide();
        };
        
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        
        function startScanning() {
            html5QrcodeScanner.start(
                { facingMode: "environment" },
                config,
                qrCodeSuccessCallback
            ).catch(err => {
                console.error(err);
                document.getElementById('qr-reader-results').innerText = "Unable to start camera. Please verify permissions.";
            });
        }
        
        startScanning();
    });

    document.getElementById('scannerModal').addEventListener('hidden.bs.modal', function () {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().catch(err => console.error(err));
        }
    });
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
