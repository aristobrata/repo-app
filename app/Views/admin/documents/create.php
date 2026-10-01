<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Upload Dokumen Baru</h4>
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form action="<?= base_url('admin/dokumen') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3"><label class="form-label">Judul</label><input type="text" name="judul" class="form-control" required value="<?= old('judul') ?>"></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Penulis</label><input type="text" name="penulis" class="form-control" required value="<?= old('penulis') ?>"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Tahun</label><input type="number" name="tahun" class="form-control" required value="<?= old('tahun') ?>"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Kategori</label>
                    <select name="kategori_id" class="form-select" required>
                        <?php foreach ($kategori as $k): ?><option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3"><label class="form-label">Kata Kunci</label><input type="text" name="kata_kunci" class="form-control" value="<?= old('kata_kunci') ?>"></div>
            <div class="mb-3"><label class="form-label">Abstrak</label><textarea name="abstrak" class="form-control" rows="4"><?= old('abstrak') ?></textarea></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Nomor Dokumen</label><input type="text" name="nomor_dokumen" class="form-control" value="<?= old('nomor_dokumen') ?>"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Tanggal Berlaku</label><input type="date" name="tanggal_berlaku" class="form-control" value="<?= old('tanggal_berlaku') ?>"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3"><label class="form-label">File Dokumen (PDF, max 50MB)</label><input type="file" name="file_dokumen" class="form-control" accept="application/pdf" required></div>
            <div class="mb-3"><label class="form-label">Jumlah Halaman Preview</label><input type="number" name="halaman_preview" class="form-control" value="5" min="1" max="20"></div>
            <div class="mb-3"><label class="form-label">Sampul / Cover (JPG/PNG, max 3MB)</label><input type="file" name="cover" class="form-control" accept="image/jpeg,image/png"></div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Upload & Proses Preview</button>
</form>
<?= $this->endSection() ?>
