<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0">Financial Summary</h1>
    <a href="<?= base_url('records/summary/export') ?>" class="btn btn-outline-success">Export CSV</a>
</div>
<?php
$renderTable = function ($title, $rows) {
    echo '<div class="card mb-4"><div class="card-header fw-semibold">'.esc($title).'</div><table class="table mb-0"><thead><tr><th>Group</th><th>Computed</th><th>Paid</th><th>Txns</th><th></th></tr></thead><tbody>';
    foreach ($rows as $r) {
        $under = (float)$r['total_paid'] < (float)$r['total_computed'];
        echo '<tr'.($under?' class="table-warning"':'').'><td>'.esc($r['group_key']).'</td>';
        echo '<td>₱'.number_format((float)$r['total_computed'],2).'</td>';
        echo '<td>₱'.number_format((float)$r['total_paid'],2).'</td>';
        echo '<td>'.(int)$r['txn_count'].'</td>';
        echo '<td>'.($under?'<span class="badge bg-warning">Underpayment</span>':'').'</td></tr>';
    }
    echo '</tbody></table></div>';
};
$renderTable('By Stall Type', $by_type);
$renderTable('By Section', $by_section);
$renderTable('By Month (Last 6 Months)', $by_month);
?>
<?= $this->endSection() ?>
