<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Kelola Dokumen</h4>
    <a href="<?= base_url('admin/dokumen/tambah') ?>" class="btn btn-primary">+ Upload Dokumen</a>
</div>
<table class="table table-striped">
    <thead><tr><th>Judul</th><th>Kategori</th><th>Tahun</th><th>Status Preview</th><th>Views</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($dokumen as $d): ?>
        <tr>
            <td><?= esc($d['judul']) ?></td>
            <td><?= esc($d['kategori_nama']) ?></td>
            <td><?= esc($d['tahun']) ?></td>
            <td><span class="badge bg-<?= $d['status_preview'] === 'ready' ? 'success' : 'secondary' ?>"><?= esc($d['status_preview']) ?></span></td>
            <td><?= (int) $d['jumlah_view'] ?></td>
            <td>
                <a href="<?= base_url('admin/dokumen/' . $d['id'] . '/lihat-lengkap') ?>" class="btn btn-sm btn-outline-success" target="_blank" title="Lihat semua halaman">👁</a>
                <a href="<?= base_url('admin/dokumen/' . $d['id'] . '/download') ?>" class="btn btn-sm btn-outline-secondary" title="Download">⬇️</a>
                <a href="<?= base_url('admin/dokumen/' . $d['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <a href="<?= base_url('admin/dokumen/' . $d['id'] . '/visibilitas') ?>" class="btn btn-sm btn-outline-secondary">Kontrol Halaman</a>
                <form action="<?= base_url('admin/dokumen/' . $d['id'] . '/hapus') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus dokumen ini?')">
                    <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?= $pager->links('default', 'default_full') ?>
<?= $this->endSection() ?>
