<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-4">Set New Rate Version</h1>
<p class="text-muted">New rates do not replace old ones. Payments always use the rate version effective on the collection date.</p>
<div class="card"><div class="card-body">
<form method="post" action="<?= base_url('rates/create') ?>">
<?= csrf_field() ?>
<div class="row g-3">
    <div class="col-md-4"><label class="form-label">Inside Rate (per sqm daily) *</label>
        <input type="number" step="0.01" name="inside_rate_per_sqm" id="inside_rate_per_sqm" data-rate-preview class="form-control"
            value="<?= old('inside_rate_per_sqm', $current['inside_rate_per_sqm'] ?? '45.00') ?>" required></div>
        <input type="hidden" name="outside_daily_rate" value="25.00">
        <input type="hidden" name="outside_weekly_rate" value="150.00">
    <div class="col-md-4"><label class="form-label">Outside Rate (per sqm daily) *</label>
        <input type="number" step="0.01" name="outside_monthly_rate" id="outside_monthly_rate" data-rate-preview class="form-control"
            value="<?= old('outside_monthly_rate', $current['outside_monthly_rate'] ?? '50.00') ?>" required></div>
    <div class="col-md-4"><label class="form-label">Ambulant Daily *</label>
        <input type="number" step="0.01" name="ambulant_daily_rate" class="form-control" value="<?= old('ambulant_daily_rate', $current['ambulant_daily_rate'] ?? '15.00') ?>" required></div>
    <div class="col-md-4"><label class="form-label">Effective Date *</label>
        <input type="date" name="effective_date" class="form-control" value="<?= old('effective_date', date('Y-m-d')) ?>" required></div>
</div>
<div class="alert alert-secondary mt-3" id="rate_preview">—</div>
<div class="mt-3"><button class="btn btn-primary">Save New Rate</button>
<a href="<?= base_url('rates') ?>" class="btn btn-outline-secondary">Cancel</a></div>
</form></div></div>
<?= $this->endSection() ?>
