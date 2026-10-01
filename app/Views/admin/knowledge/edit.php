<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Edit Knowledge Item: <?= esc($item['judul']) ?></h4>
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form action="<?= base_url('admin/knowledge/' . $item['id'] . '/update') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3"><label class="form-label">Judul</label><input type="text" name="judul" class="form-control" required value="<?= old('judul', $item['judul']) ?>"></div>
            <div class="row">
                <div class="col-md-5 mb-3"><label class="form-label">Penulis / Pengelola</label>
                    <select name="penulis_id" class="form-select" required>
                        <?php foreach ($penulis as $p): ?><option value="<?= $p['id'] ?>" <?= $p['id'] == $item['penulis_id'] ? 'selected' : '' ?>><?= esc($p['nama']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3"><label class="form-label">Divisi</label><input type="text" name="divisi" class="form-control" value="<?= old('divisi', $item['divisi']) ?>"></div>
                <div class="col-md-2 mb-3"><label class="form-label">Topik</label><input type="text" name="topik" class="form-control" value="<?= old('topik', $item['topik']) ?>"></div>
                <div class="col-md-2 mb-3"><label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="dipublikasikan" <?= $item['status'] === 'dipublikasikan' ? 'selected' : '' ?>>Dipublikasikan</option>
                        <option value="draft" <?= $item['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="diarsipkan" <?= $item['status'] === 'diarsipkan' ? 'selected' : '' ?>>Diarsipkan</option>
                    </select>
                </div>
            </div>
            <div class="mb-3"><label class="form-label">Ringkasan</label><textarea name="ringkasan" class="form-control" rows="2" required><?= old('ringkasan', $item['ringkasan']) ?></textarea></div>
            <div class="mb-3"><label class="form-label">Konten Lengkap</label><textarea name="konten" class="form-control" rows="6" required><?= old('konten', $item['konten']) ?></textarea></div>
            <div class="mb-3"><label class="form-label">Referensi</label><textarea name="referensi" class="form-control" rows="2"><?= old('referensi', $item['referensi']) ?></textarea></div>
        </div>
        <div class="col-md-4">
            <?php if (!empty($item['foto_sampul'])): ?>
                <img src="<?= base_url('knowledge-image/' . $item['foto_sampul']) ?>" class="img-fluid rounded mb-2" style="max-height:180px;">
            <?php endif; ?>
            <div class="mb-3"><label class="form-label">Ganti Sampul</label><input type="file" name="foto_sampul" class="form-control" accept="image/jpeg,image/png"></div>
            <?php if (!empty($lampiran)): ?>
                <ul class="list-group mb-2">
                    <?php foreach ($lampiran as $l): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center small">
                            <?= esc($l['nama_file']) ?>
                            <form action="<?= base_url('admin/knowledge/lampiran/' . $l['id'] . '/hapus') ?>" method="post" onsubmit="return confirm('Hapus lampiran ini?')">
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
    <a href="<?= base_url('admin/knowledge') ?>" class="btn btn-outline-secondary">Batal</a>
</form>
<?= $this->endSection() ?>
