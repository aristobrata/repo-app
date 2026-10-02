<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Kelola Aktivitas Knowledge Management</h4>
    <div>
        <a href="<?= base_url('admin/knowledge/import') ?>" class="btn btn-outline-secondary">📥 Import Excel</a>
        <a href="<?= base_url('admin/knowledge/tambah') ?>" class="btn btn-primary">+ Tambah Aktivitas</a>
    </div>
</div>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link active" href="<?= base_url('admin/knowledge') ?>">Aktivitas</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge/karyawan') ?>">Master Karyawan</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge/rekap') ?>">Rekap/Leaderboard</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge/target') ?>">Target Tahunan</a></li>
</ul>

<form method="get" class="row g-2 mb-3">
    <div class="col-md-3"><input type="text" name="keyword" class="form-control" placeholder="Cari judul event..." value="<?= esc($filters['keyword'] ?? '') ?>"></div>
    <div class="col-md-3"><input type="text" name="pillar" class="form-control" placeholder="Pillar KM" value="<?= esc($filters['pillar'] ?? '') ?>"></div>
    <div class="col-md-2"><input type="text" name="bulan" class="form-control" placeholder="Bulan" value="<?= esc($filters['bulan'] ?? '') ?>"></div>
    <div class="col-md-2"><input type="text" name="tahun" class="form-control" placeholder="Tahun" value="<?= esc($filters['tahun'] ?? '') ?>"></div>
    <div class="col-md-2"><button class="btn btn-secondary w-100">Filter</button></div>
</form>

<div class="table-responsive">
<table class="table table-striped table-sm">
    <thead><tr><th>Bulan</th><th>Pillar</th><th>Judul Event</th><th>Peserta</th><th>Departemen</th><th>Peran</th><th>Poin</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($aktivitas as $a): ?>
        <tr>
            <td><?= esc($a['bulan']) ?></td>
            <td><?= esc($a['pillar_km']) ?></td>
            <td><?= esc($a['judul_event']) ?></td>
            <td><?= esc($a['nama_peserta']) ?></td>
            <td class="small"><?= esc($a['departemen']) ?></td>
            <td><?= esc($a['peran']) ?></td>
            <td><?= (int) $a['poin'] ?></td>
            <td>
                <form action="<?= base_url('admin/knowledge/' . $a['id'] . '/hapus') ?>" method="post" onsubmit="return confirm('Hapus baris ini?')">
                    <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?= $pager->links('aktivitas', 'default_full') ?>
<?= $this->endSection() ?>
