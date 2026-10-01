<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Kelola Knowledge Management Hub</h4>
    <a href="<?= base_url('admin/knowledge/tambah') ?>" class="btn btn-primary">+ Tambah Knowledge Item</a>
</div>
<div class="table-responsive">
<table class="table table-striped">
    <thead><tr><th>Judul</th><th>Penulis</th><th>Topik</th><th>Status</th><th>Like</th><th>Views</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($knowledge as $k): ?>
        <tr>
            <td><?= esc($k['judul']) ?></td>
            <td><?= esc($k['nama_penulis']) ?></td>
            <td><?= esc($k['topik']) ?></td>
            <td><span class="badge bg-<?= $k['status'] === 'dipublikasikan' ? 'success' : 'secondary' ?>"><?= esc($k['status']) ?></span></td>
            <td><?= (int) $k['jumlah_like'] ?></td>
            <td><?= (int) $k['jumlah_view'] ?></td>
            <td>
                <a href="<?= base_url('pengetahuan/' . $k['id']) ?>" class="btn btn-sm btn-outline-secondary" target="_blank">Lihat</a>
                <a href="<?= base_url('admin/knowledge/' . $k['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="<?= base_url('admin/knowledge/' . $k['id'] . '/hapus') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus item ini?')">
                    <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?= $pager->links('default', 'default_full') ?>
<?= $this->endSection() ?>
