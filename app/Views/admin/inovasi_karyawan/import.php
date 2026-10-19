<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Import Master Karyawan Inovasi dari Excel</h4>
<?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>
<div class="card mb-3">
    <div class="card-body">
        <p class="small text-muted mb-2">Gunakan file dengan struktur seperti <code>KOMPOSISI_SEPTEMBER_Diklat_SHE.xlsx</code>, sheet <code>SEPTEMBER</code> (jika tidak ada sheet bernama itu, sheet pertama yang aktif dipakai). Urutan kolom A&ndash;P:</p>
        <p class="small"><code>No, Pers/No, ID Number, Personnel Number (berisi NAMA), Band, Position, Gender Key, Tempat Lahir, Birth date, TXT_DIR, TXT_DEPT, DEPARTEMEN, TXT_BIRO, UNIT, Nama Unit Kerja, Seksi</code></p>
        <p class="small text-warning">⚠️ Import akan MENIMPA seluruh data master karyawan inovasi yang ada (snapshot penuh).</p>
        <form action="<?= base_url('admin/inovasi-karyawan/import') ?>" method="post" enctype="multipart/form-data" class="row g-2">
            <?= csrf_field() ?>
            <div class="col-md-9"><input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required></div>
            <div class="col-md-3"><button class="btn btn-primary w-100">Import</button></div>
        </form>
    </div>
</div>
<a href="<?= base_url('admin/inovasi-karyawan') ?>" class="btn btn-outline-secondary">Kembali</a>
<?= $this->endSection() ?>
