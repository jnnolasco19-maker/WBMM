<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0">Vacant Stalls — Revenue Opportunity</h1>
    <a href="<?= base_url('records/vacant/export') ?>" class="btn btn-outline-success">Export CSV</a>
</div>
<div class="card"><div class="table-responsive">
<table class="table table-hover table-wbmm mb-0">
<thead><tr><th>Stall Code</th><th>Section</th><th>Type</th><th>SQM</th><th>Last Vendor</th></tr></thead>
<tbody>
<?php
$lastType = '';
foreach ($stalls as $s):
    if ($lastType !== $s['type']):
        if ($lastType !== '') echo '<tr><td colspan="5" class="bg-light"></td></tr>';
        $lastType = $s['type'];
    endif;
?>
<tr>
    <td><a href="<?= base_url('stalls/view/'.$s['id']) ?>"><?= esc($s['stall_code']) ?></a></td>
    <td><?= esc($s['section']) ?></td>
    <td><span class="badge badge-<?= esc($s['type']) ?>"><?= esc($s['type']) ?></span></td>
    <td><?= $s['sqm'] ? esc($s['sqm']) : '—' ?></td>
    <td><?= esc($s['last_vendor_name'] ?? '—') ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?= $this->endSection() ?>
