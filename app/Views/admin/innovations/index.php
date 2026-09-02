<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Kelola Inovasi</h4>
    <a href="<?= base_url('admin/inovasi/tambah') ?>" class="btn btn-primary">+ Tambah Inovasi</a>
</div>

<table class="table table-striped">
    <thead><tr><th>Judul</th><th>Karyawan</th><th>Divisi</th><th>Status</th><th>Like</th><th>Views</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($inovasi as $i): ?>
        <tr>
            <td><?= esc($i['judul']) ?></td>
            <td><?= esc($i['nama_karyawan']) ?></td>
            <td><?= esc($i['divisi']) ?></td>
            <td><span class="badge bg-info text-dark"><?= esc($i['status']) ?></span></td>
            <td><?= (int) $i['jumlah_like'] ?></td>
            <td><?= (int) $i['jumlah_view'] ?></td>
            <td>
                <a href="<?= base_url('inovasi/' . $i['id']) ?>" class="btn btn-sm btn-outline-secondary" target="_blank">Lihat</a>
                <a href="<?= base_url('admin/inovasi/' . $i['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="<?= base_url('admin/inovasi/' . $i['id'] . '/hapus') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus inovasi ini beserta lampirannya?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?= $pager->links('default', 'default_full') ?>

<?= $this->endSection() ?>
