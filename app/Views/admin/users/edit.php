<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Edit User: <?= esc($user['nama']) ?></h4>
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form action="<?= base_url('admin/users/' . $user['id'] . '/update') ?>" method="post" style="max-width:500px;">
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">NIP</label><input type="text" name="nip" class="form-control" value="<?= old('nip', $user['nip']) ?>"></div>
    <div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" name="nama" class="form-control" required value="<?= old('nama', $user['nama']) ?>"></div>
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?= old('email', $user['email']) ?>"></div>
    <div class="mb-3"><label class="form-label">Divisi</label><input type="text" name="divisi" class="form-control" value="<?= old('divisi', $user['divisi']) ?>"></div>
    <div class="mb-3"><label class="form-label">Role</label>
        <select name="role" class="form-select">
            <option value="karyawan" <?= $user['role'] === 'karyawan' ? 'selected' : '' ?>>Karyawan</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="super_admin" <?= $user['role'] === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">Batal</a>
</form>
<?= $this->endSection() ?>
