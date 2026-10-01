<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Tambah Knowledge Item</h4>
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form action="<?= base_url('admin/knowledge') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3"><label class="form-label">Judul</label><input type="text" name="judul" class="form-control" required value="<?= old('judul') ?>"></div>
            <div class="row">
                <div class="col-md-5 mb-3"><label class="form-label">Penulis / Pengelola</label>
                    <select name="penulis_id" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($penulis as $p): ?><option value="<?= $p['id'] ?>"><?= esc($p['nama']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3"><label class="form-label">Divisi</label><input type="text" name="divisi" class="form-control" value="<?= old('divisi') ?>"></div>
                <div class="col-md-2 mb-3"><label class="form-label">Topik</label><input type="text" name="topik" class="form-control" placeholder="Best Practice, dll" value="<?= old('topik') ?>"></div>
                <div class="col-md-2 mb-3"><label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="dipublikasikan">Dipublikasikan</option><option value="draft">Draft</option><option value="diarsipkan">Diarsipkan</option>
                    </select>
                </div>
            </div>
            <div class="mb-3"><label class="form-label">Ringkasan</label><textarea name="ringkasan" class="form-control" rows="2" required><?= old('ringkasan') ?></textarea></div>
            <div class="mb-3"><label class="form-label">Konten Lengkap</label><textarea name="konten" class="form-control" rows="6" required><?= old('konten') ?></textarea></div>
            <div class="mb-3"><label class="form-label">Referensi (opsional)</label><textarea name="referensi" class="form-control" rows="2"><?= old('referensi') ?></textarea></div>
        </div>
        <div class="col-md-4">
            <div class="mb-3"><label class="form-label">Sampul</label><input type="file" name="foto_sampul" class="form-control" accept="image/jpeg,image/png"></div>
            <div class="mb-3"><label class="form-label">Lampiran Dokumen Pendukung</label><input type="file" name="lampiran[]" class="form-control" multiple></div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="<?= base_url('admin/knowledge') ?>" class="btn btn-outline-secondary">Batal</a>
</form>
<?= $this->endSection() ?>
