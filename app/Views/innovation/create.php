<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Ajukan Inovasi Baru</h4>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= base_url('inovasi') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Judul Inovasi</label>
        <input type="text" name="judul" class="form-control" required value="<?= old('judul') ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Divisi</label>
        <input type="text" name="divisi" class="form-control" value="<?= old('divisi') ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Deskripsi Masalah</label>
        <textarea name="deskripsi_masalah" class="form-control" rows="3" required><?= old('deskripsi_masalah') ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Solusi Inovatif</label>
        <textarea name="solusi_inovatif" class="form-control" rows="3" required><?= old('solusi_inovatif') ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Dampak/Manfaat bagi Perusahaan</label>
        <textarea name="dampak_manfaat" class="form-control" rows="3" required><?= old('dampak_manfaat') ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Lampiran Dokumen Pendukung (opsional, bisa lebih dari satu)</label>
        <input type="file" name="lampiran[]" class="form-control" multiple>
    </div>
    <button type="submit" class="btn btn-primary">Ajukan</button>
</form>

<?= $this->endSection() ?>
