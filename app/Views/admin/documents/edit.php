<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Edit Dokumen: <?= esc($dokumen['judul']) ?></h4>
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form action="<?= base_url('admin/dokumen/' . $dokumen['id'] . '/update') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3"><label class="form-label">Judul</label><input type="text" name="judul" class="form-control" required value="<?= old('judul', $dokumen['judul']) ?>"></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Penulis</label><input type="text" name="penulis" class="form-control" required value="<?= old('penulis', $dokumen['penulis']) ?>"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Tahun</label><input type="number" name="tahun" class="form-control" required value="<?= old('tahun', $dokumen['tahun']) ?>"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Kategori</label>
                    <select name="kategori_id" class="form-select" required>
                        <?php foreach ($kategori as $k): ?><option value="<?= $k['id'] ?>" <?= $k['id'] == $dokumen['kategori_id'] ? 'selected' : '' ?>><?= esc($k['nama']) ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3"><label class="form-label">Kata Kunci</label><input type="text" name="kata_kunci" class="form-control" value="<?= old('kata_kunci', $dokumen['kata_kunci']) ?>"></div>
            <div class="mb-3"><label class="form-label">Abstrak</label><textarea name="abstrak" class="form-control" rows="4"><?= old('abstrak', $dokumen['abstrak']) ?></textarea></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Nomor Dokumen</label><input type="text" name="nomor_dokumen" class="form-control" value="<?= old('nomor_dokumen', $dokumen['nomor_dokumen']) ?>"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Tanggal Berlaku</label><input type="date" name="tanggal_berlaku" class="form-control" value="<?= old('tanggal_berlaku', $dokumen['tanggal_berlaku']) ?>"></div>
            </div>
        </div>
        <div class="col-md-4">
            <?php if (!empty($dokumen['cover_thumbnail'])): ?>
                <p class="form-label mb-1">Sampul Saat Ini</p>
                <img src="<?= base_url('cover-image/' . $dokumen['cover_thumbnail']) ?>" class="img-fluid rounded mb-2" style="max-height:180px;">
            <?php endif; ?>
            <div class="mb-3"><label class="form-label">Ganti Sampul</label><input type="file" name="cover" class="form-control" accept="image/jpeg,image/png"></div>
            <div class="alert alert-secondary small">Untuk mengganti file PDF asli, hapus dokumen ini lalu upload ulang.</div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="<?= base_url('admin/dokumen') ?>" class="btn btn-outline-secondary">Batal</a>
</form>
<?= $this->endSection() ?>
