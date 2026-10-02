<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Import Data Inovasi dari Excel</h4>

<div class="card mb-3">
    <div class="card-body">
        <h6>Format File yang Diharapkan</h6>
        <p class="small text-muted mb-2">
            File harus berisi kolom persis seperti berikut (urutan A sampai U), 1 baris = 1 anggota tim.
            Baris dengan <code>nama_tim</code> + <code>judul_inovasi</code> yang sama akan otomatis digabung
            menjadi 1 inovasi dengan banyak anggota tim.
        </p>
        <code class="small">
            id, kategori_inovasi, tanggal_registrasi, nama_tim, judul_inovasi, area_improvement,
            unit_dept_area_implementasi, unit_biro_area_implementasi, nama_personil, nik, struktur_tim,
            org_unit, biaya_project, saving, opp_lost, revenue, total_benefit, status_saat_ini,
            keterangan, hyperlink_dokumen, tahun
        </code>
        <p class="small text-warning mt-2 mb-0">⚠️ Data dengan kombinasi nama_tim + judul_inovasi yang sudah ada akan otomatis dilewati (tidak dobel).</p>
    </div>
</div>

<form action="<?= base_url('admin/inovasi/import') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">File Excel (.xlsx)</label>
        <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required>
    </div>
    <button type="submit" class="btn btn-primary">Mulai Import</button>
    <a href="<?= base_url('admin/inovasi') ?>" class="btn btn-outline-secondary">Batal</a>
</form>
<?= $this->endSection() ?>
