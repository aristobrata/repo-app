<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Import Data Knowledge Management dari Excel</h4>
<p class="text-muted small mb-4">File sumber punya 3 sheet terpisah. Import masing-masing sheet lewat form di bawah ini.</p>

<div class="card mb-3">
    <div class="card-body">
        <h6>1. Import Aktivitas KM <span class="badge bg-info text-dark">sheet: fact_aktivitas_km</span></h6>
        <p class="small text-muted">Data baru akan DITAMBAHKAN (tidak menimpa data lama).</p>
        <form action="<?= base_url('admin/knowledge/import/aktivitas') ?>" method="post" enctype="multipart/form-data" class="row g-2">
            <?= csrf_field() ?>
            <div class="col-md-6"><input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required></div>
            <div class="col-md-3"><input type="text" name="tahun" class="form-control" placeholder="Tahun data (mis. 2026)" value="<?= date('Y') ?>"></div>
            <div class="col-md-3"><button class="btn btn-primary w-100">Import</button></div>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h6>2. Import Master Karyawan <span class="badge bg-info text-dark">sheet: dim_karyawan</span></h6>
        <p class="small text-warning">⚠️ Akan MENIMPA seluruh data master karyawan lama (snapshot penuh).</p>
        <form action="<?= base_url('admin/knowledge/import/karyawan') ?>" method="post" enctype="multipart/form-data" class="row g-2">
            <?= csrf_field() ?>
            <div class="col-md-9"><input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required></div>
            <div class="col-md-3"><button class="btn btn-primary w-100">Import</button></div>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h6>3. Import Rekap/Leaderboard <span class="badge bg-info text-dark">sheet: rekap_karyawan</span></h6>
        <p class="small text-warning">⚠️ Akan MENIMPA seluruh data rekap lama (dipakai Leaderboard Dashboard).</p>
        <form action="<?= base_url('admin/knowledge/import/rekap') ?>" method="post" enctype="multipart/form-data" class="row g-2">
            <?= csrf_field() ?>
            <div class="col-md-9"><input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required></div>
            <div class="col-md-3"><button class="btn btn-primary w-100">Import</button></div>
        </form>
    </div>
</div>

<a href="<?= base_url('admin/knowledge') ?>" class="btn btn-outline-secondary">Kembali</a>
<?= $this->endSection() ?>
