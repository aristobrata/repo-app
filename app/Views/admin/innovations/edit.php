<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Edit Inovasi: <?= esc($inovasi['judul']) ?></h4>
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form action="<?= base_url('admin/inovasi/' . $inovasi['id'] . '/update') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3"><label class="form-label">Judul Inovasi</label><input type="text" name="judul" class="form-control" required value="<?= old('judul', $inovasi['judul']) ?>"></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Atas Nama Karyawan</label>
                    <select name="karyawan_id" class="form-select" required>
                        <?php foreach ($karyawan as $k): ?><option value="<?= $k['id'] ?>" <?= $k['id'] == $inovasi['karyawan_id'] ? 'selected' : '' ?>><?= esc($k['nama']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3"><label class="form-label">Divisi</label><input type="text" name="divisi" class="form-control" value="<?= old('divisi', $inovasi['divisi']) ?>"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="diajukan" <?= $inovasi['status'] === 'diajukan' ? 'selected' : '' ?>>Diajukan</option>
                        <option value="diverifikasi" <?= $inovasi['status'] === 'diverifikasi' ? 'selected' : '' ?>>Diverifikasi</option>
                        <option value="diterapkan" <?= $inovasi['status'] === 'diterapkan' ? 'selected' : '' ?>>Diterapkan</option>
                    </select>
                </div>
            </div>
            <div class="mb-3"><label class="form-label">Deskripsi Masalah</label><textarea name="deskripsi_masalah" class="form-control" rows="3" required><?= old('deskripsi_masalah', $inovasi['deskripsi_masalah']) ?></textarea></div>
            <div class="mb-3"><label class="form-label">Solusi Inovatif</label><textarea name="solusi_inovatif" class="form-control" rows="3" required><?= old('solusi_inovatif', $inovasi['solusi_inovatif']) ?></textarea></div>
            <div class="mb-3"><label class="form-label">Dampak/Manfaat</label><textarea name="dampak_manfaat" class="form-control" rows="3" required><?= old('dampak_manfaat', $inovasi['dampak_manfaat']) ?></textarea></div>
        </div>
        <div class="col-md-4">
            <?php if (!empty($inovasi['foto_ilustrasi'])): ?>
                <img src="<?= base_url('inovasi-image/' . $inovasi['foto_ilustrasi']) ?>" class="img-fluid rounded mb-2" style="max-height:180px;">
            <?php endif; ?>
            <div class="mb-3"><label class="form-label">Ganti Sampul</label><input type="file" name="foto_ilustrasi" class="form-control" accept="image/jpeg,image/png"></div>
            <?php if (!empty($lampiran)): ?>
                <ul class="list-group mb-2">
                    <?php foreach ($lampiran as $l): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center small">
                            <?= esc($l['nama_file']) ?>
                            <form action="<?= base_url('admin/inovasi/lampiran/' . $l['id'] . '/hapus') ?>" method="post" onsubmit="return confirm('Hapus lampiran ini?')">
                                <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <div class="mb-3"><label class="form-label">Tambah Lampiran Baru</label><input type="file" name="lampiran[]" class="form-control" multiple></div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="<?= base_url('admin/inovasi') ?>" class="btn btn-outline-secondary">Batal</a>
</form>
<?= $this->endSection() ?>
