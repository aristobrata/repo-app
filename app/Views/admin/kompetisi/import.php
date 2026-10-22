<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Import Kompetisi Inovasi dari Excel</h4>
<?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>
<div class="card mb-3">
    <div class="card-body">
        <p class="small text-muted mb-2">Gunakan file seperti <code>kompetisi_inovasi_database_ready.xlsx</code>, sheet <code>Data_Import</code> (jika tidak ada, sheet aktif dipakai). Kolom A&ndash;J:</p>
        <p class="small"><code>id, nik, nama_karyawan, nama_tim, jenis_penghargaan, judul_inovasi, event_kompetisi, penyelenggara, tanggal_penghargaan, link_dokumen</code></p>
        <p class="small text-muted">1 baris = 1 personil. Baris dengan tim, judul, event, penghargaan, dan tanggal yang sama digabung menjadi 1 entri kompetisi. Entri yang sudah ada <strong>dilewati</strong> (data lama tidak ditimpa), jadi aman diimpor ulang.</p>
        <form action="<?= base_url('admin/kompetisi/import') ?>" method="post" enctype="multipart/form-data" class="row g-2">
            <?= csrf_field() ?>
            <div class="col-md-9"><input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required></div>
            <div class="col-md-3"><button class="btn btn-primary w-100">Import</button></div>
        </form>
    </div>
</div>
<a href="<?= base_url('admin/kompetisi') ?>" class="btn btn-outline-secondary">Kembali</a>
<?= $this->endSection() ?>
