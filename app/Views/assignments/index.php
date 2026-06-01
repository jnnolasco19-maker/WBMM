<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0">Vendor-Stall Assignments</h1>
    <a href="<?= base_url('assignments/create') ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Assignment</a>
</div>

<?php
$active      = array_filter($assignments, static fn($a) => $a['status'] === 'active');
$terminated  = array_filter($assignments, static fn($a) => $a['status'] === 'terminated');
?>

<ul class="nav nav-tabs mb-3" id="assignmentTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-pane" type="button" role="tab" aria-controls="active-pane" aria-selected="true">
            Active Assignments <span class="badge bg-success ms-1"><?= count($active) ?></span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="terminated-tab" data-bs-toggle="tab" data-bs-target="#terminated-pane" type="button" role="tab" aria-controls="terminated-pane" aria-selected="false">
            Terminated History <span class="badge bg-secondary ms-1"><?= count($terminated) ?></span>
        </button>
    </li>
</ul>

<div class="tab-content" id="assignmentTabsContent">
    <!-- Active Assignments Pane -->
    <div class="tab-pane fade show active" id="active-pane" role="tabpanel" aria-labelledby="active-tab">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover table-wbmm mb-0">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>Stall</th>
                            <th>Section</th>
                            <th>Permit</th>
                            <th>Expiry</th>
                            <th>Assigned</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($active)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No active assignments found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($active as $a): ?>
                            <tr>
                                <td><a href="<?= base_url('vendors/view/' . $a['vendor_id']) ?>"><?= esc($a['vendor_name']) ?></a></td>
                                <td><?= esc($a['stall_code']) ?></td>
                                <td><?= esc($a['section']) ?></td>
                                <td><?= esc($a['permit_no'] ?? '—') ?></td>
                                <td><?= esc($a['permit_expiry'] ?? '—') ?></td>
                                <td><?= esc($a['assigned_date']) ?></td>
                                <td><span class="badge bg-success"><?= esc($a['status']) ?></span></td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-end">
                                        <?php if (in_array($user_role, ['admin', 'staff'], true)): ?>
                                        <a href="<?= base_url('assignments/edit/' . $a['id']) ?>" class="btn btn-sm btn-outline-primary">Renew Permit</a>
                                        <?php endif; ?>
                                        <?php if ($user_role === 'admin'): ?>
                                        <form method="post" action="<?= base_url('assignments/terminate/' . $a['id']) ?>" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-outline-danger" data-confirm="Terminate this assignment? The stall will become vacant.">Terminate</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Terminated History Pane -->
    <div class="tab-pane fade" id="terminated-pane" role="tabpanel" aria-labelledby="terminated-tab">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover table-wbmm mb-0">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>Stall</th>
                            <th>Section</th>
                            <th>Permit</th>
                            <th>Assigned</th>
                            <th>Terminated</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($terminated)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No terminated assignments in history.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($terminated as $a): ?>
                            <tr>
                                <td><a href="<?= base_url('vendors/view/' . $a['vendor_id']) ?>"><?= esc($a['vendor_name']) ?></a></td>
                                <td><?= esc($a['stall_code']) ?></td>
                                <td><?= esc($a['section']) ?></td>
                                <td><?= esc($a['permit_no'] ?? '—') ?></td>
                                <td><?= esc($a['assigned_date']) ?></td>
                                <td><?= esc($a['terminated_date'] ?? '—') ?></td>
                                <td><span class="badge bg-secondary"><?= esc($a['status']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
