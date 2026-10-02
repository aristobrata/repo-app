<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Kelola Inovasi</h4>
    <div>
        <a href="<?= base_url('admin/inovasi/import') ?>" class="btn btn-outline-secondary">📥 Import Excel</a>
        <a href="<?= base_url('admin/inovasi/tambah') ?>" class="btn btn-primary">+ Tambah Inovasi</a>
    </div>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-md-4"><input type="text" name="keyword" class="form-control" placeholder="Cari judul..." value="<?= esc($filters['keyword'] ?? '') ?>"></div>
    <div class="col-md-3"><input type="text" name="kategori" class="form-control" placeholder="Kategori" value="<?= esc($filters['kategori'] ?? '') ?>"></div>
    <div class="col-md-2"><input type="text" name="tahun" class="form-control" placeholder="Tahun" value="<?= esc($filters['tahun'] ?? '') ?>"></div>
    <div class="col-md-2"><button class="btn btn-secondary w-100">Filter</button></div>
</form>

<div class="table-responsive">
<table class="table table-striped">
    <thead><tr><th>Judul</th><th>Kategori</th><th>Tim</th><th>Tahun</th><th>Status</th><th>Total Benefit</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($inovasi as $i): ?>
        <tr>
            <td><?= esc($i['judul_inovasi']) ?></td>
            <td><?= esc($i['kategori_inovasi']) ?></td>
            <td><?= esc($i['nama_tim']) ?></td>
            <td><?= esc($i['tahun']) ?></td>
            <td><?= esc($i['status_saat_ini']) ?></td>
            <td>Rp <?= number_format($i['total_benefit'] ?? 0, 0, ',', '.') ?></td>
            <td>
                <a href="<?= base_url('inovasi/' . $i['id']) ?>" class="btn btn-sm btn-outline-secondary" target="_blank">Lihat</a>
                <a href="<?= base_url('admin/inovasi/' . $i['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="<?= base_url('admin/inovasi/' . $i['id'] . '/hapus') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus inovasi ini beserta anggota timnya?')">
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
