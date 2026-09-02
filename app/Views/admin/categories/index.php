<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Kelola Kategori Dokumen</h4>
    <a href="<?= base_url('admin/kategori/tambah') ?>" class="btn btn-primary">+ Tambah Kategori</a>
</div>

<table class="table table-striped">
    <thead><tr><th>Nama</th><th>Slug</th><th>Jumlah Dokumen</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($kategori as $k): ?>
        <tr>
            <td><?= esc($k['nama']) ?></td>
            <td><code><?= esc($k['slug']) ?></code></td>
            <td><?= (int) $k['jumlah_dokumen'] ?></td>
            <td>
                <a href="<?= base_url('admin/kategori/' . $k['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="<?= base_url('admin/kategori/' . $k['id'] . '/hapus') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger" <?= $k['jumlah_dokumen'] > 0 ? 'disabled title="Masih dipakai dokumen"' : '' ?>>Hapus</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
