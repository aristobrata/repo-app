<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$edit = !empty($item);
$v = fn(string $k, $default = '') => old($k, $edit ? ($item[$k] ?? $default) : $default);
$errors = session()->getFlashdata('errors');
?>
<h4 class="mb-3"><?= $edit ? 'Edit Aktivitas KM' : 'Tambah Aktivitas KM' ?></h4>
<?php if ($errors): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form action="<?= $edit ? base_url('admin/knowledge/' . $item['id'] . '/update') : base_url('admin/knowledge') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <h6 class="text-muted text-uppercase small mt-2">Kegiatan</h6>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Pillar KM</label>
            <input type="text" name="pillar_km" class="form-control" list="dl-pillar" placeholder="LS-Learn & Share" value="<?= esc($v('pillar_km')) ?>">
            <datalist id="dl-pillar"><?php foreach ($opsi_pillar as $o): ?><option value="<?= esc($o) ?>"><?php endforeach; ?></datalist>
        </div>
        <div class="col-md-4 mb-3"><label class="form-label">Aktivitas</label><input type="text" name="aktivitas" class="form-control" value="<?= esc($v('aktivitas')) ?>"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Subactivity</label><input type="text" name="subactivity" class="form-control" value="<?= esc($v('subactivity')) ?>"></div>
    </div>
    <div class="mb-3"><label class="form-label">Judul / Event <span class="text-danger">*</span></label><input type="text" name="judul_event" class="form-control" required value="<?= esc($v('judul_event')) ?>"></div>
    <div class="row">
        <div class="col-md-3 mb-3"><label class="form-label">Tanggal Score</label><input type="date" name="tanggal_score" class="form-control" value="<?= esc($v('tanggal_score')) ?>"></div>
        <div class="col-md-3 mb-3"><label class="form-label">Tanggal Dibuat</label><input type="date" name="tanggal_create" class="form-control" value="<?= esc($v('tanggal_create')) ?>"></div>
        <div class="col-md-2 mb-3"><label class="form-label">Bulan</label><input type="text" name="bulan" class="form-control" placeholder="otomatis" value="<?= esc($v('bulan')) ?>"></div>
        <div class="col-md-2 mb-3"><label class="form-label">Tahun</label><input type="text" name="tahun" class="form-control" placeholder="otomatis" value="<?= esc($v('tahun', $edit ? '' : date('Y'))) ?>"></div>
        <div class="col-md-2 mb-3"><label class="form-label">Poin</label><input type="number" name="poin" class="form-control" value="<?= esc($v('poin', 0)) ?>"></div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Tempat</label><input type="text" name="tempat" class="form-control" value="<?= esc($v('tempat')) ?>"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Tipe Aktivitas</label><input type="text" name="activity_type" class="form-control" value="<?= esc($v('activity_type', 'event')) ?>"></div>
    </div>

    <h6 class="text-muted text-uppercase small mt-2">Peserta</h6>
    <div class="form-text mb-2">Kalau NIK terdaftar di Master Karyawan KM, kolom nama, NIP, direktorat, departemen, biro, unit, dan bidang yang dikosongkan akan terisi otomatis saat disimpan.</div>
    <div class="row">
        <div class="col-md-3 mb-3"><label class="form-label">NIK</label><input type="text" name="nik" class="form-control" value="<?= esc($v('nik')) ?>"></div>
        <div class="col-md-3 mb-3"><label class="form-label">NIP</label><input type="text" name="nip" class="form-control" value="<?= esc($v('nip')) ?>"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Nama Peserta <span class="text-danger">*</span></label><input type="text" name="nama_peserta" class="form-control" value="<?= esc($v('nama_peserta')) ?>"></div>
        <div class="col-md-2 mb-3"><label class="form-label">Peran</label>
            <?php $peran = $v('peran', 'Peserta'); ?>
            <select name="peran" class="form-select">
                <?php foreach (['Peserta', 'Pembicara'] as $o): ?><option value="<?= $o ?>" <?= $peran === $o ? 'selected' : '' ?>><?= $o ?></option><?php endforeach; ?>
                <?php if ($peran && !in_array($peran, ['Peserta', 'Pembicara'], true)): ?><option value="<?= esc($peran) ?>" selected><?= esc($peran) ?></option><?php endif; ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3"><label class="form-label">Direktorat</label><input type="text" name="direktorat" class="form-control" value="<?= esc($v('direktorat')) ?>"></div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Departemen</label>
            <input type="text" name="departemen" class="form-control" list="dl-dept" value="<?= esc($v('departemen')) ?>">
            <datalist id="dl-dept"><?php foreach ($opsi_dept as $o): ?><option value="<?= esc($o) ?>"><?php endforeach; ?></datalist>
        </div>
        <div class="col-md-4 mb-3"><label class="form-label">Biro</label><input type="text" name="biro" class="form-control" value="<?= esc($v('biro')) ?>"></div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Unit Organisasi</label><input type="text" name="org_unit" class="form-control" value="<?= esc($v('org_unit')) ?>"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Bidang</label><input type="text" name="bidang" class="form-control" value="<?= esc($v('bidang')) ?>"></div>
    </div>

    <h6 class="text-muted text-uppercase small mt-2">Berkas</h6>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Lampiran Materi/Dokumentasi <span class="badge bg-secondary">Tidak Wajib</span></label>
            <input type="file" name="file_dokumen" class="form-control">
            <div class="form-text">PDF, Word, PowerPoint, Excel, ZIP/RAR, atau gambar. Maks. 10 MB.</div>
            <?php if ($edit && !empty($item['file_dokumen'])): ?>
                <div class="form-text">File saat ini: <a href="<?= base_url('library/km/' . $item['id'] . '/download') ?>">unduh</a>
                    &middot; <label><input type="checkbox" name="hapus_file_dokumen" value="1"> hapus file ini</label></div>
            <?php endif; ?>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Sampul / Cover <span class="badge bg-secondary">Tidak Wajib</span></label>
            <input type="file" name="cover" class="form-control" accept="image/jpeg,image/png" id="input-cover">
            <div class="form-text">JPG/PNG, maks. 3 MB. Jika kosong, Library memakai gambar default.</div>
            <?php $coverSaatIni = $edit && !empty($item['cover']) ? base_url('library-image/' . $item['cover']) : ''; ?>
            <img id="preview-cover" src="<?= $coverSaatIni ?>" class="rounded mt-2 border" style="max-height:160px; <?= $coverSaatIni ? '' : 'display:none;' ?>">
            <?php if ($coverSaatIni): ?><div class="form-text"><label><input type="checkbox" name="hapus_cover" value="1"> hapus sampul ini</label></div><?php endif; ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="<?= base_url('admin/knowledge') ?>" class="btn btn-outline-secondary">Batal</a>
</form>
<script>
document.getElementById('input-cover').addEventListener('change', function () {
    var f = this.files && this.files[0], img = document.getElementById('preview-cover');
    if (f) { img.src = window.URL.createObjectURL(f); img.style.display = ''; }
});
</script>
<?= $this->endSection() ?>
