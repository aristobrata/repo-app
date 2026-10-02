<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Tambah Inovasi</h4>
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form action="<?= base_url('admin/inovasi') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3"><label class="form-label">Judul Inovasi</label><input type="text" name="judul_inovasi" class="form-control" required value="<?= old('judul_inovasi') ?>"></div>
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">Kategori</label><input type="text" name="kategori_inovasi" class="form-control" placeholder="FI, TPP, PKM, dll" value="<?= old('kategori_inovasi') ?>"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Nama Tim</label><input type="text" name="nama_tim" class="form-control" value="<?= old('nama_tim') ?>"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Tahun</label><input type="text" name="tahun" class="form-control" value="<?= old('tahun', date('Y')) ?>"></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Tanggal Registrasi</label><input type="date" name="tanggal_registrasi" class="form-control" value="<?= old('tanggal_registrasi') ?>"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Status Saat Ini</label><input type="text" name="status_saat_ini" class="form-control" value="<?= old('status_saat_ini') ?>"></div>
            </div>
            <div class="mb-3"><label class="form-label">Area Improvement</label><input type="text" name="area_improvement" class="form-control" value="<?= old('area_improvement') ?>"></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Unit/Dept Area Implementasi</label><input type="text" name="unit_dept_area_implementasi" class="form-control" value="<?= old('unit_dept_area_implementasi') ?>"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Unit/Biro Area Implementasi</label><input type="text" name="unit_biro_area_implementasi" class="form-control" value="<?= old('unit_biro_area_implementasi') ?>"></div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3"><label class="form-label">Biaya Project</label><input type="number" step="0.01" name="biaya_project" class="form-control" value="<?= old('biaya_project', 0) ?>"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Saving</label><input type="number" step="0.01" name="saving" class="form-control" value="<?= old('saving', 0) ?>"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Opp. Lost</label><input type="number" step="0.01" name="opp_lost" class="form-control" value="<?= old('opp_lost', 0) ?>"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Revenue</label><input type="number" step="0.01" name="revenue" class="form-control" value="<?= old('revenue', 0) ?>"></div>
            </div>
            <div class="mb-3"><label class="form-label">Total Benefit</label><input type="number" step="0.01" name="total_benefit" class="form-control" value="<?= old('total_benefit', 0) ?>"></div>
            <div class="mb-3"><label class="form-label">Keterangan</label><textarea name="keterangan" class="form-control" rows="2"><?= old('keterangan') ?></textarea></div>
            <div class="mb-3"><label class="form-label">Hyperlink Dokumen (referensi link/path lama, opsional)</label><input type="text" name="hyperlink_dokumen" class="form-control" value="<?= old('hyperlink_dokumen') ?>"></div>

            <h6 class="mt-4">Anggota Tim</h6>
            <div id="anggota-wrapper">
                <div class="row anggota-row mb-2">
                    <div class="col-md-4"><input type="text" name="anggota_nama[]" class="form-control" placeholder="Nama Personil"></div>
                    <div class="col-md-2"><input type="text" name="anggota_nik[]" class="form-control" placeholder="NIK"></div>
                    <div class="col-md-3"><input type="text" name="anggota_peran[]" class="form-control" placeholder="Ketua/Sekretaris/Anggota"></div>
                    <div class="col-md-3"><input type="text" name="anggota_unit[]" class="form-control" placeholder="Org Unit"></div>
                </div>
            </div>
            <button type="button" id="btn-tambah-anggota" class="btn btn-sm btn-outline-secondary mb-3">+ Tambah Anggota</button>
        </div>

        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">File Dokumen <span class="badge bg-secondary">Tidak Wajib</span></label>
                <input type="file" name="file_dokumen" class="form-control">
                <div class="form-text">Opsional -- boleh dikosongkan jika hanya mengandalkan import Excel / hyperlink referensi lama.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Sampul / Cover (opsional)</label>
                <input type="file" name="cover" class="form-control" accept="image/jpeg,image/png">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="<?= base_url('admin/inovasi') ?>" class="btn btn-outline-secondary">Batal</a>
</form>

<?= $this->section('scripts') ?>
<script>
document.getElementById('btn-tambah-anggota').addEventListener('click', function () {
    const wrapper = document.getElementById('anggota-wrapper');
    const row = wrapper.querySelector('.anggota-row').cloneNode(true);
    row.querySelectorAll('input').forEach(i => i.value = '');
    wrapper.appendChild(row);
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
