<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0">Transaction Records</h1>
    <a href="<?= base_url('records/export?'.http_build_query($filters ?? [])) ?>" class="btn btn-outline-success"><i class="fa-solid fa-download"></i> Export CSV</a>
</div>
<form class="row g-2 mb-3" method="get">
    <div class="col-md-2"><input type="date" name="date_from" class="form-control" value="<?= esc($filters['date_from'] ?? '') ?>"></div>
    <div class="col-md-2"><input type="date" name="date_to" class="form-control" value="<?= esc($filters['date_to'] ?? '') ?>"></div>
    <div class="col-md-2"><input type="text" name="search" class="form-control" placeholder="Search..." value="<?= esc($filters['search'] ?? '') ?>"></div>
    <div class="col-md-2"><select name="stall_type" class="form-select"><option value="">All Types</option>
        <?php foreach (['inside','outside','ambulant'] as $t): ?><option value="<?= $t ?>" <?= ($filters['stall_type']??'')===$t?'selected':'' ?>><?= ucfirst($t) ?></option><?php endforeach; ?>
    </select></div>
    <div class="col-md-2"><select name="collected_by" class="form-select"><option value="">All Collectors</option>
        <?php foreach ($collectors as $c): ?><option value="<?= $c['id'] ?>" <?= ($filters['collected_by']??'')==$c['id']?'selected':'' ?>><?= esc($c['name']) ?></option><?php endforeach; ?>
    </select></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
</form>
<div class="card"><div class="table-responsive">
<table class="table table-hover table-wbmm mb-0">
<thead><tr><th>Ref</th><th>Vendor</th><th>Stall</th><th>Type</th><th>Period</th><th>Computed</th><th>Paid</th><th>Collector</th><th>Date</th></tr></thead>
<tbody>
<?php foreach ($payments as $p): ?>
<tr>
    <td><a href="<?= base_url('payments/receipt/'.$p['id']) ?>"><?= esc($p['reference_no']) ?></a></td>
    <td><?= esc($p['vendor_name']) ?></td>
    <td><?= esc($p['stall_code'] ?? '—') ?></td>
    <td><?= esc($p['stall_type'] ?? 'ambulant') ?></td>
    <td class="small"><?= esc($p['period_start']) ?> – <?= esc($p['period_end']) ?></td>
    <td>₱<?= number_format((float)$p['computed_amount'],2) ?></td>
    <td class="<?= (float)$p['amount_paid']<(float)$p['computed_amount']?'text-danger':'' ?>">₱<?= number_format((float)$p['amount_paid'],2) ?></td>
    <td><?= esc($p['collector_name']) ?></td>
    <td><?= date('M d, Y', strtotime($p['payment_date'])) ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<div class="card-footer"><?= $pager->links() ?></div></div>
<?= $this->endSection() ?>
