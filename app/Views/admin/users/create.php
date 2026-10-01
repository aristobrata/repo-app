<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Tambah Akun Karyawan</h4>
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form action="<?= base_url('admin/users') ?>" method="post" style="max-width:500px;">
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">NIP</label><input type="text" name="nip" class="form-control" value="<?= old('nip') ?>"></div>
    <div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" name="nama" class="form-control" required value="<?= old('nama') ?>"></div>
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?= old('email') ?>"></div>
    <div class="mb-3"><label class="form-label">Password Awal</label><input type="password" name="password" class="form-control" required minlength="8"></div>
    <div class="mb-3"><label class="form-label">Divisi</label><input type="text" name="divisi" class="form-control" value="<?= old('divisi') ?>"></div>
    <div class="mb-3"><label class="form-label">Role</label>
        <select name="role" class="form-select">
            <option value="karyawan">Karyawan</option><option value="admin">Admin</option><option value="super_admin">Super Admin</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
</form>
<?= $this->endSection() ?>
