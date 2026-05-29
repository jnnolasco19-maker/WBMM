<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="<?= base_url('payments') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
            <h1 class="h3 fw-bold mb-0">Record Rental Payment</h1>
        </div>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>

        <!-- Capture URL query param for dynamic auto-selection -->
        <?php 
        $queryVendorId = isset($_GET['vendor_id']) ? (int) $_GET['vendor_id'] : 0; 
        ?>

        <div class="card card-custom">
            <div class="card-body p-4 p-md-5">
                <form action="<?= base_url('payments/create') ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <div class="row g-4">
                        <!-- Select Vendor -->
                        <div class="col-12">
                            <label for="vendor_id" class="form-label fw-semibold text-dark">Stall Vendor <span class="text-danger">*</span></label>
                            <select id="vendor_id" name="vendor_id" class="form-select <?= isset($errors['vendor_id']) ? 'is-invalid' : '' ?>" required>
                                <option value="">-- Choose Vendor --</option>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?= $vendor['id'] ?>" <?= (old('vendor_id', $queryVendorId) == $vendor['id']) ? 'selected' : '' ?>>
                                        <?= esc($vendor['name']) ?> (Stall: <?= esc($vendor['stall_number']) ?> — <?= esc($vendor['section']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['vendor_id'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['vendor_id']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Amount -->
                        <div class="col-12 col-md-6">
                            <label for="amount" class="form-label fw-semibold text-dark">Amount Collected (₱) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">₱</span>
                                <input type="number" id="amount" name="amount" step="0.01" min="0" class="form-control <?= isset($errors['amount']) ? 'is-invalid' : '' ?>" placeholder="0.00" value="<?= esc(old('amount')) ?>" required>
                                <?php if (isset($errors['amount'])): ?>
                                    <div class="invalid-feedback"><?= esc($errors['amount']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Payment Type -->
                        <div class="col-12 col-md-6">
                            <label for="payment_type" class="form-label fw-semibold text-dark">Payment Recurrence <span class="text-danger">*</span></label>
                            <select id="payment_type" name="payment_type" class="form-select <?= isset($errors['payment_type']) ? 'is-invalid' : '' ?>" required>
                                <option value="daily" <?= old('payment_type') === 'daily' ? 'selected' : '' ?>>Daily Rent</option>
                                <option value="weekly" <?= old('payment_type') === 'weekly' ? 'selected' : '' ?>>Weekly Lease</option>
                                <option value="monthly" <?= old('payment_type') === 'monthly' ? 'selected' : '' ?>>Monthly Lease</option>
                            </select>
                            <?php if (isset($errors['payment_type'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['payment_type']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Period covered start -->
                        <div class="col-12 col-md-6">
                            <label for="period_start" class="form-label fw-semibold text-dark">Lease Period Start Covered <span class="text-danger">*</span></label>
                            <input type="date" id="period_start" name="period_start" class="form-control <?= isset($errors['period_start']) ? 'is-invalid' : '' ?>" value="<?= esc(old('period_start', date('Y-m-d'))) ?>" required>
                            <?php if (isset($errors['period_start'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['period_start']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Period covered end -->
                        <div class="col-12 col-md-6">
                            <label for="period_end" class="form-label fw-semibold text-dark">Lease Period End Covered <span class="text-danger">*</span></label>
                            <input type="date" id="period_end" name="period_end" class="form-control <?= isset($errors['period_end']) ? 'is-invalid' : '' ?>" value="<?= esc(old('period_end', date('Y-m-d'))) ?>" required>
                            <?php if (isset($errors['period_end'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['period_end']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="form-label fw-semibold text-dark">Collection Notes / Comments</label>
                            <textarea id="notes" name="notes" class="form-control <?= isset($errors['notes']) ? 'is-invalid' : '' ?>" rows="3" placeholder="Reference remarks..." maxlength="1000"><?= esc(old('notes')) ?></textarea>
                            <?php if (isset($errors['notes'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['notes']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-5">
                        <button type="submit" class="btn btn-gradient-primary px-4 py-2">
                            <i class="fa-solid fa-calculator me-2"></i> Register Payment
                        </button>
                        <a href="<?= base_url('payments') ?>" class="btn btn-light border px-4 py-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<?= $this->section('script') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodStart = document.getElementById('period_start');
    const periodEnd   = document.getElementById('period_end');
    const typeSelect  = document.getElementById('payment_type');

    // Auto-compute period covered dates for cashier convenience
    function autoAlignDates() {
        if (!periodStart.value) return;

        const start = new Date(periodStart.value);
        let end = new Date(start);

        if (typeSelect.value === 'daily') {
            // End same day
            end = start;
        } else if (typeSelect.value === 'weekly') {
            // End 6 days later
            end.setDate(start.getDate() + 6);
        } else if (typeSelect.value === 'monthly') {
            // End 1 month later less 1 day
            end.setMonth(start.getMonth() + 1);
            end.setDate(end.getDate() - 1);
        }

        // Format Date back into YYYY-MM-DD
        const yyyy = end.getFullYear();
        const mm = String(end.getMonth() + 1).padStart(2, '0');
        const dd = String(end.getDate()).padStart(2, '0');
        periodEnd.value = `${yyyy}-${mm}-${dd}`;
    }

    typeSelect.addEventListener('change', autoAlignDates);
    periodStart.addEventListener('change', autoAlignDates);
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
