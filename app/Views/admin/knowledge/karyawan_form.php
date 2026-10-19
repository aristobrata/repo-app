<?php
$edit = !empty($karyawan);
$v = fn(string $f) => old($f, $edit ? ($karyawan[$f] ?? '') : '');
$action = $edit ? base_url('admin/knowledge/karyawan/' . $karyawan['id'] . '/update') : base_url('admin/knowledge/karyawan');
$fields = [
    ['perner', 'NIK (Perner)'], ['id_number', 'NIP (ID Number)'], ['personnel_number', 'Nama Lengkap *'], ['band', 'Band'],
    ['direktorat', 'Direktorat'], ['departemen', 'Departemen'], ['nama_unit_kerja', 'Nama Unit Kerja'], ['biro', 'Biro'],
    ['org_unit', 'Org Unit'], ['bidang', 'Bidang'],
];
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3"><?= $edit ? 'Edit' : 'Tambah' ?> Karyawan (Master KM)</h4>
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form action="<?= $action ?>" method="post" style="max-width:800px;">
    <?= csrf_field() ?>
    <div class="row g-3">
    <?php foreach ($fields as [$name, $label]): ?>
        <div class="col-md-6">
            <label class="form-label"><?= $label ?></label>
            <input type="text" name="<?= $name ?>" class="form-control" value="<?= esc($v($name)) ?>" <?= $name === 'personnel_number' ? 'required' : '' ?>>
        </div>
    <?php endforeach; ?>
    </div>
    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/knowledge/karyawan') ?>" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
