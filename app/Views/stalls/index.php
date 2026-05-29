<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title) ?> — WBMM</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <style>body { background-color: #f4f6f8; }</style>
</head>
<body>

<?= view('layouts/navbar', ['user_name' => $user_name, 'user_role' => $user_role]) ?>

<main class="container py-4">

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('message')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Stalls</h1>
        <?php if (in_array($user_role, ['admin', 'manager'], true)): ?>
            <a href="<?= base_url('stalls/create') ?>" class="btn btn-primary btn-sm">+ Add Stall</a>
        <?php endif; ?>
    </div>

    <form method="get" action="<?= base_url('stalls') ?>" class="row g-2 mb-3">
        <div class="col-12 col-md-5">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Search stall number, location…"
                   value="<?= esc($search) ?>">
        </div>
        <div class="col-6 col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="occupied" <?= $status === 'occupied' ? 'selected' : '' ?>>Occupied</option>
                <option value="vacant"   <?= $status === 'vacant'   ? 'selected' : '' ?>>Vacant</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            <a href="<?= base_url('stalls') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Stall Number</th>
                    <th>Location</th>
                    <th>Size</th>
                    <th>Status</th>
                    <th>Assigned Vendor</th>
                    <th class="text-center">QR Ticket</th>
                    <?php if (in_array($user_role, ['admin', 'manager'], true)): ?>
                        <th class="text-center">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($stalls)): ?>
                    <tr>
                        <td colspan="<?= in_array($user_role, ['admin', 'manager'], true) ? 8 : 7 ?>" class="text-center text-muted py-4">
                            No stalls found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($stalls as $stall): ?>
                        <tr>
                            <td><?= esc($stall['id']) ?></td>
                            <td><?= esc($stall['stall_number']) ?></td>
                            <td><?= esc($stall['location'] ?? '—') ?></td>
                            <td><?= esc($stall['size'] ?? '—') ?></td>
                            <td>
                                <span class="badge <?= $stall['status'] === 'occupied' ? 'bg-danger' : 'bg-success' ?>">
                                    <?= esc(ucfirst($stall['status'])) ?>
                                </span>
                            </td>
                            <td><?= esc($stall['vendor_name'] ?? 'Unassigned') ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary py-0" 
                                        style="font-size: 0.8rem;"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#qrModal_<?= $stall['id'] ?>">
                                    🎫 View QR
                                </button>
                            </td>
                            <?php if (in_array($user_role, ['admin', 'manager'], true)): ?>
                                <td class="text-center">
                                    <a href="<?= base_url('stalls/edit/' . $stall['id']) ?>"
                                       class="btn btn-warning btn-sm">Edit</a>
                                    <?php if ($user_role === 'admin'): ?>
                                        <form action="<?= base_url('stalls/delete/' . $stall['id']) ?>"
                                              method="post" class="d-inline"
                                              onsubmit="return confirm('Delete this stall?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>

                        <!-- QR Permit Modal -->
                        <div class="modal fade" id="qrModal_<?= $stall['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-body p-4 text-center" id="printable_permit_<?= $stall['id'] ?>">
                                        
                                        <!-- Header GenSan LGU -->
                                        <div class="mb-3 border-bottom pb-2">
                                            <div class="fw-bold text-uppercase text-primary small" style="letter-spacing:1px;">General Santos City LGU</div>
                                            <div class="fw-bold text-dark h6 mb-0">GenSan Public Market Operations</div>
                                            <span class="badge bg-success text-uppercase mt-1" style="font-size:0.65rem;">Official Stall Permit</span>
                                        </div>

                                        <!-- QR Display -->
                                        <div class="p-3 bg-light border rounded shadow-sm d-inline-block mb-3">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&color=198754&data=StallID:<?= $stall['id'] ?>|StallNum:<?= urlencode($stall['stall_number']) ?>" alt="Stall QR Pass" class="img-fluid">
                                        </div>

                                        <!-- Stall Details -->
                                        <div class="text-start p-2 border rounded bg-light small mb-3">
                                            <div class="row g-2">
                                                <div class="col-5 text-muted">Stall Number:</div>
                                                <div class="col-7 fw-bold text-dark text-end"><?= esc($stall['stall_number']) ?></div>
                                                
                                                <div class="col-5 text-muted">Location:</div>
                                                <div class="col-7 text-dark text-end"><?= esc($stall['location'] ?? 'Wet Section') ?></div>
                                                
                                                <div class="col-5 text-muted">Stall Size:</div>
                                                <div class="col-7 text-dark text-end"><?= esc($stall['size'] ?? '—') ?></div>
                                                
                                                <div class="col-5 text-muted">Leaseholder:</div>
                                                <div class="col-7 fw-bold text-success text-end"><?= esc($stall['vendor_name'] ?? 'Unassigned') ?></div>
                                            </div>
                                        </div>

                                        <!-- Footer / PalengQR -->
                                        <div class="text-muted small border-top pt-2">
                                            <div class="fw-semibold text-primary">Paleng-QR Ph Compliant</div>
                                            <div>Scan for daily Arkalaba audit tracking.</div>
                                        </div>

                                    </div>
                                    <div class="modal-footer border-0 p-2 d-flex justify-content-between bg-light rounded-bottom">
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="printPermit(<?= $stall['id'] ?>)">Print Ticket</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pager): ?>
        <div class="d-flex justify-content-center">
            <?= $pager->links('stalls', 'bootstrap_pagination') ?>
        </div>
    <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmFXFMrWCU3FA0e6bKIHFORSMR9"
        crossorigin="anonymous"></script>

<script>
function printPermit(id) {
    const printContent = document.getElementById('printable_permit_' + id).innerHTML;
    const originalContent = document.body.innerHTML;
    
    document.body.innerHTML = `
        <div style="width: 320px; margin: 40px auto; font-family: sans-serif; text-align: center;">
            ${printContent}
        </div>
    `;
    
    window.print();
    
    document.body.innerHTML = originalContent;
    window.location.reload();
}
</script>
</body>
</html>
