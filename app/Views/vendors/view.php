<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h3 mb-1"><?= esc($vendor['first_name'].' '.$vendor['last_name']) ?></h1>
        <p class="text-muted mb-0"><?= esc($vendor['vendor_no']) ?> · <span class="badge badge-<?= esc($vendor['type']) ?>"><?= esc($vendor['type']) ?></span> · <?= esc($vendor['status']) ?></p>
    </div>
    <?php if (in_array($user_role, ['admin','collector'], true)): ?>
    <a href="<?= base_url('payments/create?vendor_id='.$vendor['id']) ?>" class="btn btn-success"><i class="fa-solid fa-receipt"></i> Collect Arkalaba</a>
    <?php endif; ?>
</div>

<div class="row g-4">
<div class="col-md-5">
<div class="card mb-3"><div class="card-body">
    <p><strong>Business:</strong> <?= esc($vendor['business_name'] ?? '—') ?></p>
    <p><strong>Contact:</strong> <?= esc($vendor['contact'] ?? '—') ?></p>
    <p><strong>ID:</strong> <?= esc($vendor['id_type'] ?? '—') ?> <?= esc($vendor['id_number'] ?? '') ?></p>
    <p class="mb-0"><strong>Address:</strong> <?= esc($vendor['address'] ?? '—') ?></p>
</div></div>
<div class="card bg-light"><div class="card-body text-center">
    <div class="text-muted small">Paid This Month</div>
    <div class="h3 text-success mb-0">₱<?= number_format($total_this_month, 2) ?></div>
</div></div>
</div>
<div class="col-md-7">
<div class="card mb-3"><div class="card-header">Active Stall Assignments</div>
<?php if (empty($active_stalls)): ?><div class="card-body text-muted">No active stalls.</div>
<?php else: ?>
<table class="table mb-0"><thead><tr><th>Stall</th><th>Permit</th><th>Expiry</th><?php if (in_array($user_role, ['admin', 'staff'], true)): ?><th class="text-end">Action</th><?php endif; ?></tr></thead><tbody>
<?php foreach ($active_stalls as $s): ?>
<tr class="<?= in_array($s['id'], $overdue_ids ?? []) ? 'table-danger' : '' ?>">
    <td><a href="<?= base_url('stalls/view/'.$s['stall_id']) ?>"><?= esc($s['stall_code']) ?></a></td>
    <td><?= esc($s['permit_no'] ?? '—') ?></td>
    <td><?= esc($s['permit_expiry'] ?? '—') ?></td>
    <?php if (in_array($user_role, ['admin', 'staff'], true)): ?>
    <td class="text-end">
        <a href="<?= base_url('assignments/edit/' . $s['id']) ?>" class="btn btn-sm btn-outline-primary py-0">Renew</a>
    </td>
    <?php endif; ?>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php endif; ?>
</div>
<?php if (!empty($past_assignments)): ?>
<div class="card mb-3"><div class="card-header">Past Assignments</div>
<table class="table table-sm mb-0"><tbody>
<?php foreach ($past_assignments as $a): ?>
<tr><td><?= esc($a['stall_code']) ?></td><td><?= esc($a['status']) ?></td><td><?= esc($a['terminated_date'] ?? $a['assigned_date']) ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<div class="card"><div class="card-header">Payment History</div>
<table class="table table-sm mb-0"><thead><tr><th>Ref</th><th>Stall</th><th>Paid</th><th>Date</th></tr></thead><tbody>
<?php foreach ($payments as $p): ?>
<tr><td><a href="<?= base_url('payments/receipt/'.$p['id']) ?>"><?= esc($p['reference_no']) ?></a></td>
<td><?= esc($p['stall_code'] ?? 'Ambulant') ?></td><td>₱<?= number_format((float)$p['amount_paid'],2) ?></td>
<td><?= date('M d, Y', strtotime($p['payment_date'])) ?></td></tr>
<?php endforeach; ?>
<?php if (empty($payments)): ?><tr><td colspan="4" class="text-muted text-center">No payments</td></tr><?php endif; ?>
</tbody></table></div>
</div>
</div>
<a href="<?= base_url('vendors') ?>" class="btn btn-outline-secondary mt-3">← Back</a>
<?= $this->endSection() ?>
