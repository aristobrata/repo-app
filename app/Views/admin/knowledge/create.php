<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Tambah Aktivitas KM</h4>
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form action="<?= base_url('admin/knowledge') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-md-4 mb-3"><label class="form-label">Bulan</label><input type="text" name="bulan" class="form-control" placeholder="JANUARI" value="<?= old('bulan') ?>"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Pillar KM</label><input type="text" name="pillar_km" class="form-control" placeholder="LS-Learn & Share" value="<?= old('pillar_km') ?>"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Tahun</label><input type="text" name="tahun" class="form-control" value="<?= old('tahun', date('Y')) ?>"></div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Aktivitas</label><input type="text" name="aktivitas" class="form-control" value="<?= old('aktivitas') ?>"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Subactivity</label><input type="text" name="subactivity" class="form-control" value="<?= old('subactivity') ?>"></div>
    </div>
    <div class="mb-3"><label class="form-label">Judul / Event</label><input type="text" name="judul_event" class="form-control" required value="<?= old('judul_event') ?>"></div>
    <div class="row">
        <div class="col-md-4 mb-3"><label class="form-label">Tanggal Score</label><input type="date" name="tanggal_score" class="form-control" value="<?= old('tanggal_score') ?>"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Tempat</label><input type="text" name="tempat" class="form-control" value="<?= old('tempat') ?>"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Poin</label><input type="number" name="poin" class="form-control" value="<?= old('poin', 0) ?>"></div>
    </div>
    <div class="row">
        <div class="col-md-3 mb-3"><label class="form-label">NIK</label><input type="text" name="nik" class="form-control" value="<?= old('nik') ?>"></div>
        <div class="col-md-3 mb-3"><label class="form-label">NIP</label><input type="text" name="nip" class="form-control" value="<?= old('nip') ?>"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Nama Peserta</label><input type="text" name="nama_peserta" class="form-control" required value="<?= old('nama_peserta') ?>"></div>
        <div class="col-md-2 mb-3"><label class="form-label">Peran</label>
            <select name="peran" class="form-select"><option value="Peserta">Peserta</option><option value="Pembicara">Pembicara</option></select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Direktorat</label><input type="text" name="direktorat" class="form-control" value="<?= old('direktorat') ?>"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Departemen</label><input type="text" name="departemen" class="form-control" value="<?= old('departemen') ?>"></div>
    </div>
    <div class="mb-3">
        <label class="form-label">Lampiran Materi/Dokumentasi <span class="badge bg-secondary">Tidak Wajib</span></label>
        <input type="file" name="file_dokumen" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="<?= base_url('admin/knowledge') ?>" class="btn btn-outline-secondary">Batal</a>
</form>
<?= $this->endSection() ?>
