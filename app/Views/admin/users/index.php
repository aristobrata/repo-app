<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Kelola User</h4>
    <a href="<?= base_url('admin/users/tambah') ?>" class="btn btn-primary">+ Tambah User</a>
</div>

<table class="table table-striped">
    <thead>
    <tr><th>Nama</th><th>Email</th><th>Divisi</th><th>Role</th><th>Status</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= esc($u['nama']) ?></td>
            <td><?= esc($u['email']) ?></td>
            <td><?= esc($u['divisi']) ?></td>
            <td><?= esc($u['role']) ?></td>
            <td><span class="badge bg-<?= $u['status'] === 'aktif' ? 'success' : 'secondary' ?>"><?= esc($u['status']) ?></span></td>
            <td>
                <a href="<?= base_url('admin/users/' . $u['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="<?= base_url('admin/users/' . $u['id'] . '/reset-password') ?>" method="post" class="d-inline" onsubmit="return confirm('Reset password user ini?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-secondary">Reset Password</button>
                </form>
                <form action="<?= base_url('admin/users/' . $u['id'] . '/toggle-status') ?>" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-warning">
                        <?= $u['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>
                    </button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?= $pager->links('default', 'default_full') ?>

<?= $this->endSection() ?>
