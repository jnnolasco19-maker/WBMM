<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Vendors Directory</h1>
        <p class="text-muted mb-0">Manage and audit registered stall holders.</p>
    </div>
    <a href="<?= base_url('vendors/create') ?>" class="btn btn-gradient-primary rounded-pill px-4">
        <i class="fa-solid fa-plus me-2"></i> Register Vendor
    </a>
</div>

<style>
    .table-row-expired {
        background-color: rgba(239, 68, 68, 0.04) !important;
    }
    .table-row-expiring {
        background-color: rgba(245, 158, 11, 0.04) !important;
    }
</style>

<!-- SEARCH AND FILTERS -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form action="<?= base_url('vendors') ?>" method="get" class="row g-3">
            <div class="col-12 col-md-3">
                <label for="search" class="form-label small fw-semibold text-muted">Search Query</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" id="search" name="search" class="form-control border-start-0" placeholder="Name, stall..." value="<?= esc($search) ?>">
                </div>
            </div>
            
            <div class="col-6 col-md-2">
                <label for="section" class="form-label small fw-semibold text-muted">Section</label>
                <select id="section" name="section" class="form-select">
                    <option value="">-- All --</option>
                    <option value="Dry Goods" <?= $section === 'Dry Goods' ? 'selected' : '' ?>>Dry Goods</option>
                    <option value="Wet Market" <?= $section === 'Wet Market' ? 'selected' : '' ?>>Wet Market</option>
                    <option value="Livestock" <?= $section === 'Livestock' ? 'selected' : '' ?>>Livestock</option>
                    <option value="Commercial" <?= $section === 'Commercial' ? 'selected' : '' ?>>Commercial</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label for="status" class="form-label small fw-semibold text-muted">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">-- All --</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="col-12 col-md-3 d-flex align-items-center pt-md-4">
                <div class="form-check form-switch pt-2">
                    <input class="form-check-input" type="checkbox" role="switch" name="expiring_soon" id="expiring_soon" value="1" <?= $expiring_soon ? 'checked' : '' ?>>
                    <label class="form-check-label small fw-semibold text-muted" for="expiring_soon">Show Expiring/Expired</label>
                </div>
            </div>

            <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold">Filter</button>
                <a href="<?= base_url('vendors') ?>" class="btn btn-light border w-100 fw-bold">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- VENDORS DIRECTORY TABLE -->
<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">Stall Holder</th>
                        <th class="py-3">Stall Number</th>
                        <th class="py-3">Section</th>
                        <th class="py-3">Contact</th>
                        <th class="py-3">Permit Expiry</th>
                        <th class="py-3">Status</th>
                        <?php if ($user_role === 'admin'): ?>
                            <th class="px-4 py-3 text-end">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vendors)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-folder-open fa-3x mb-3 d-block text-muted"></i>
                                No registered vendors match your filters.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $today = date('Y-m-d');
                        $thirtyDaysFromNow = date('Y-m-d', strtotime('+30 days'));
                        foreach ($vendors as $vendor): 
                            $isExpired = $vendor['permit_expiry'] < $today;
                            $isExpiringSoon = !$isExpired && ($vendor['permit_expiry'] <= $thirtyDaysFromNow);
                            
                            $rowClass = '';
                            if ($isExpired) {
                                $rowClass = 'table-row-expired';
                            } elseif ($isExpiringSoon) {
                                $rowClass = 'table-row-expiring';
                            }
                        ?>
                            <tr class="<?= $rowClass ?>">
                                <td class="px-4 fw-semibold text-dark"><?= esc($vendor['name']) ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="fa-solid fa-hashtag text-muted me-1"></i><?= esc($vendor['stall_number']) ?></span>
                                </td>
                                <td><?= esc($vendor['section']) ?></td>
                                <td><?= esc($vendor['contact']) ?: '<em class="text-muted">None</em>' ?></td>
                                <td>
                                    <?php if ($isExpired): ?>
                                        <span class="badge badge-expired px-2.5 py-1.5" title="Permit has expired!">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i><?= date('M d, Y', strtotime($vendor['permit_expiry'])) ?> (Expired)
                                        </span>
                                    <?php elseif ($isExpiringSoon): ?>
                                        <span class="badge bg-warning text-dark border border-warning border-opacity-25 px-2.5 py-1.5" title="Permit is expiring soon!">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i><?= date('M d, Y', strtotime($vendor['permit_expiry'])) ?> (Expiring)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-active px-2.5 py-1.5" title="Permit is active">
                                            <i class="fa-solid fa-circle-check me-1"></i><?= date('M d, Y', strtotime($vendor['permit_expiry'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($vendor['status'] === 'active'): ?>
                                        <span class="badge bg-success-subtle text-success px-2 py-1 border border-success border-opacity-25 rounded-pill text-uppercase" style="font-size:0.75rem;">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1 border border-secondary border-opacity-25 rounded-pill text-uppercase" style="font-size:0.75rem;">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($user_role === 'admin'): ?>
                                    <td class="px-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="<?= base_url('vendors/edit/' . $vendor['id']) ?>" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1.5 rounded-pill px-3">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                                <span>Edit</span>
                                            </a>
                                            <form action="<?= base_url('vendors/delete/' . $vendor['id']) ?>" method="post" class="m-0" onsubmit="return confirm('Are you absolutely sure you want to delete vendor <?= esc($vendor['name']) ?>? This action is irreversible.');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1.5 rounded-pill px-3">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- PAGINATION -->
        <?php if (! empty($pager)): ?>
            <div class="d-flex justify-content-center py-4 bg-light border-top">
                <?= $pager->links('vendors', 'bootstrap_pagination') ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?= $this->endSection() ?>
