<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="fw-bold mb-3">Edit Dokumen</h4>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" action="<?= base_url('admin/dokumen/update/' . $dokumen['id']) ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label">Judul</label>
        <input type="text" name="judul" class="form-control" value="<?= old('judul', $dokumen['judul']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3"><?= old('deskripsi', $dokumen['deskripsi']) ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="kategori_id" class="form-select">
          <option value="">- Pilih Kategori -</option>
          <?php foreach ($kategori as $k): ?>
            <option value="<?= $k['id'] ?>" <?= $dokumen['kategori_id'] == $k['id'] ? 'selected' : '' ?>><?= esc($k['nama']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Ganti File Dokumen (opsional, biarkan kosong jika tidak diubah)</label>
        <input type="file" name="file_asli" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Ganti Cover / Sampul (opsional)</label>
        <input type="file" name="cover" class="form-control" accept="image/*">
      </div>
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      <a href="<?= base_url('admin/dokumen') ?>" class="btn btn-outline-secondary">Batal</a>
    </form>
  </div>
</div>

<?= $this->endSection() ?>
