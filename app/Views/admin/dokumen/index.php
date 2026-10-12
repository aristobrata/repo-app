<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="fw-bold mb-0">Kelola Dokumen</h4>
  <a href="<?= base_url('admin/dokumen/create') ?>" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Dokumen</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <table class="table table-hover align-middle">
      <thead>
        <tr><th>Judul</th><th>Kategori</th><th>Status Preview</th><th>Views</th><th>Downloads</th><th class="text-end">Aksi</th></tr>
      </thead>
      <tbody>
        <?php foreach ($dokumen as $d): ?>
          <tr>
            <td><?= esc($d['judul']) ?></td>
            <td><?= esc($d['nama_kategori'] ?? '-') ?></td>
            <td><span class="badge bg-secondary"><?= esc($d['status_preview']) ?></span></td>
            <td><?= $d['jumlah_view'] ?></td>
            <td><?= $d['jumlah_download'] ?></td>
            <td class="text-end">
              <a href="<?= base_url('admin/dokumen/edit/' . $d['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
              <a href="<?= base_url('admin/dokumen/delete/' . $d['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus dokumen ini?')">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <?= $pager->links('dokumen', 'pager/custom') ?>
  </div>
</div>

<?= $this->endSection() ?>
