<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= base_url('records') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
    <h1 class="h3 fw-bold mb-0">System Audit Logs</h1>
</div>

<div class="card card-custom">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h5 class="card-title fw-bold mb-0 text-dark">Audit Trail Console</h5>
        <p class="text-muted small mb-0">Security tracking logs and history of all administrative alterations in the WBMM system.</p>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">Logged At</th>
                        <th class="py-3">Administrator</th>
                        <th class="py-3">System Action Logged</th>
                        <th class="py-3">Impacted Table</th>
                        <th class="px-4 py-3 text-end">Record ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-clipboard-question fa-3x mb-3 d-block text-muted"></i>
                                No audit log trail has been recorded yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="px-4 text-muted small"><?= date('M d, Y H:i:s', strtotime($log['created_at'])) ?></td>
                                <td>
                                    <span class="fw-semibold text-dark d-block"><?= esc($log['user_name']) ?: 'System / Guest' ?></span>
                                    <small class="text-muted text-uppercase" style="font-size:0.65rem;"><?= esc($log['user_role']) ?: 'Anonymous' ?></small>
                                </td>
                                <td class="fw-semibold text-dark"><?= esc($log['action']) ?></td>
                                <td>
                                    <span class="badge bg-light text-secondary border px-2 py-1.5 font-monospace" style="font-family:monospace;"><?= esc($log['table_affected']) ?></span>
                                </td>
                                <td class="px-4 text-end text-muted font-monospace" style="font-family:monospace;">
                                    <?= $log['record_id'] ? '#' . esc($log['record_id']) : '<em class="text-muted small">—</em>' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
