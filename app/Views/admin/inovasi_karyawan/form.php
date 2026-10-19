<?php
$edit = !empty($karyawan);
$v = fn(string $f) => old($f, $edit ? ($karyawan[$f] ?? '') : '');
$action = $edit ? base_url('admin/inovasi-karyawan/' . $karyawan['id'] . '/update') : base_url('admin/inovasi-karyawan');
$fields = [
    ['perner', 'Pers/No (NIK)'], ['id_number', 'ID Number (NIP)'], ['nama', 'Nama Lengkap *'], ['band', 'Band'],
    ['jabatan', 'Jabatan (Position)'], ['gender', 'Gender'], ['tempat_lahir', 'Tempat Lahir'], ['tanggal_lahir', 'Tanggal Lahir'],
    ['direktorat', 'Direktorat'], ['departemen', 'Departemen (Indonesia)'], ['departemen_en', 'Departemen (Inggris)'], ['biro', 'Biro'],
    ['nama_unit_kerja', 'Nama Unit Kerja (Indonesia)'], ['unit_kerja_en', 'Unit (Inggris)'], ['seksi', 'Seksi'],
];
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3"><?= $edit ? 'Edit' : 'Tambah' ?> Karyawan Inovasi</h4>
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form action="<?= $action ?>" method="post" style="max-width:800px;">
    <?= csrf_field() ?>
    <div class="row g-3">
    <?php foreach ($fields as [$name, $label]): ?>
        <div class="col-md-6">
            <label class="form-label"><?= $label ?></label>
            <?php if ($name === 'gender'): ?>
                <select name="gender" class="form-select">
                    <option value="">-</option>
                    <?php foreach (['Male', 'Female'] as $g): ?><option value="<?= $g ?>" <?= $v('gender') === $g ? 'selected' : '' ?>><?= $g ?></option><?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="<?= $name === 'tanggal_lahir' ? 'date' : 'text' ?>" name="<?= $name ?>" class="form-control" value="<?= esc($v($name)) ?>" <?= $name === 'nama' ? 'required' : '' ?>>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>
    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/inovasi-karyawan') ?>" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
