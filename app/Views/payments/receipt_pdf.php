<!DOCTYPE html>
<html><head><meta charset="UTF-8"><style>
body{font-family:DejaVu Sans,sans-serif;font-size:12px;color:#111}
.header{text-align:center;margin-bottom:20px}
h4,h5{margin:2px 0}
table{width:100%;margin:8px 0}
td{padding:4px 0}
.total{font-size:16px;font-weight:bold}
hr{border:1px solid #333}
</style></head><body>
<div class="header">
    <h4>REPUBLIC OF THE PHILIPPINES</h4>
    <h4>CITY GOVERNMENT OF GENERAL SANTOS</h4>
    <h4><strong>PUBLIC MARKET — ARKALABA RESIBO</strong></h4>
</div>
<hr>
<table>
<tr><td width="40%"><strong>Reference No:</strong></td><td><?= esc($payment['reference_no']) ?></td></tr>
<tr><td><strong>Date:</strong></td><td><?= date('F d, Y', strtotime($payment['payment_date'])) ?></td></tr>
<tr><td><strong>Vendor:</strong></td><td><?= esc($payment['vendor_name']) ?> (<?= esc($payment['vendor_no']) ?>)</td></tr>
<tr><td><strong>Stall:</strong></td><td><?= esc($payment['stall_code'] ?? 'Ambulant') ?></td></tr>
<tr><td><strong>Rate Used:</strong></td><td>₱<?= number_format((float)$payment['rate_used'], 2) ?></td></tr>
<tr><td><strong>Period:</strong></td><td><?= esc($payment['period_start']) ?> — <?= esc($payment['period_end']) ?></td></tr>
<tr><td><strong>Computed:</strong></td><td>₱<?= number_format((float)$payment['computed_amount'], 2) ?></td></tr>
<tr><td class="total"><strong>Amount Paid:</strong></td><td class="total">₱<?= number_format((float)$payment['amount_paid'], 2) ?></td></tr>
<tr><td><strong>Collected By:</strong></td><td><?= esc($payment['collector_name']) ?></td></tr>
</table>
<?php if ($payment['notes']): ?><p><strong>Notes:</strong> <?= esc($payment['notes']) ?></p><?php endif; ?>
<p style="text-align:center;margin-top:40px;font-size:10px;color:#666">WBMM — General Santos City Public Market</p>
</body></html>
