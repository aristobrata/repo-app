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
            <div class="mb-3"><label class="form-label">Judul Inovasi</label><input type="text" name="judul" class="form-control" required value="<?= old('judul') ?>"></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Atas Nama Karyawan</label>
                    <select name="karyawan_id" class="form-select" required>
                        <option value="">-- Pilih Karyawan --</option>
                        <?php foreach ($karyawan as $k): ?><option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?> (<?= esc($k['divisi']) ?>)</option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3"><label class="form-label">Divisi</label><input type="text" name="divisi" class="form-control" value="<?= old('divisi') ?>"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="diajukan">Diajukan</option><option value="diverifikasi">Diverifikasi</option><option value="diterapkan">Diterapkan</option>
                    </select>
                </div>
            </div>
            <div class="mb-3"><label class="form-label">Deskripsi Masalah</label><textarea name="deskripsi_masalah" class="form-control" rows="3" required><?= old('deskripsi_masalah') ?></textarea></div>
            <div class="mb-3"><label class="form-label">Solusi Inovatif</label><textarea name="solusi_inovatif" class="form-control" rows="3" required><?= old('solusi_inovatif') ?></textarea></div>
            <div class="mb-3"><label class="form-label">Dampak/Manfaat bagi Perusahaan</label><textarea name="dampak_manfaat" class="form-control" rows="3" required><?= old('dampak_manfaat') ?></textarea></div>
        </div>
        <div class="col-md-4">
            <div class="mb-3"><label class="form-label">Sampul / Ilustrasi</label><input type="file" name="foto_ilustrasi" class="form-control" accept="image/jpeg,image/png"></div>
            <div class="mb-3"><label class="form-label">Lampiran Dokumen Pendukung</label><input type="file" name="lampiran[]" class="form-control" multiple></div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="<?= base_url('admin/inovasi') ?>" class="btn btn-outline-secondary">Batal</a>
</form>
<?= $this->endSection() ?>
