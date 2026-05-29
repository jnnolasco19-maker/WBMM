<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h3 mb-4">Edit User — <?= esc($user['name']) ?></h1>
<div class="card"><div class="card-body">
<form method="post" action="<?= base_url('users/edit/'.$user['id']) ?>">
<?= csrf_field() ?>
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" value="<?= esc(old('name',$user['name'])) ?>" required></div>
    <div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="<?= esc(old('email',$user['email'])) ?>" required></div>
    <div class="col-md-4"><label class="form-label">New Password</label><input type="password" name="password" class="form-control" placeholder="Leave blank to keep current"></div>
    <div class="col-md-4"><label class="form-label">Role *</label>
        <select name="role" class="form-select" required>
            <?php foreach (['admin','supervisor','collector','staff'] as $r): ?>
            <option value="<?= $r ?>" <?= old('role',$user['role'])===$r?'selected':'' ?>><?= ucfirst($r) ?></option>
            <?php endforeach; ?>
        </select></div>
    <div class="col-md-4"><label class="form-label">Status *</label>
        <select name="status" class="form-select">
            <option value="active" <?= old('status',$user['status'])==='active'?'selected':'' ?>>Active</option>
            <option value="inactive" <?= old('status',$user['status'])==='inactive'?'selected':'' ?>>Inactive</option>
        </select></div>
</div>
<div class="mt-4"><button class="btn btn-primary">Update User</button>
<a href="<?= base_url('users') ?>" class="btn btn-outline-secondary">Cancel</a></div>
</form></div></div>
<?= $this->endSection() ?>
