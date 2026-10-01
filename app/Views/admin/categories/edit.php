<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Edit Kategori: <?= esc($kategori['nama']) ?></h4>
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form action="<?= base_url('admin/kategori/' . $kategori['id'] . '/update') ?>" method="post" style="max-width:500px;">
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Nama Kategori</label><input type="text" name="nama" class="form-control" required value="<?= old('nama', $kategori['nama']) ?>"></div>
    <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3"><?= old('deskripsi', $kategori['deskripsi']) ?></textarea></div>
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="<?= base_url('admin/kategori') ?>" class="btn btn-outline-secondary">Batal</a>
</form>
<?= $this->endSection() ?>
