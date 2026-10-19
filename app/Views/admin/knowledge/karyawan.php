<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Master Data Karyawan (Knowledge Management)</h4>
    <div class="d-flex gap-2">
        <form action="<?= base_url('admin/knowledge/karyawan/sinkron-band') ?>" method="post" class="d-inline" onsubmit="return confirm('Isi kolom Band yang kosong dari data Rekap/Leaderboard (cocok berdasarkan NIK, lalu NIP)?')">
            <?= csrf_field() ?><button class="btn btn-outline-primary">🔗 Sinkron Band dari Rekap</button>
        </form>
        <a href="<?= base_url('admin/knowledge/karyawan/tambah') ?>" class="btn btn-primary">+ Tambah Karyawan</a>
        <a href="<?= base_url('admin/knowledge/karyawan/export') . '?' . http_build_query(array_filter($filters ?? [])) ?>" class="btn btn-outline-success">📊 Export Excel</a>
    </div>
</div>
<?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge') ?>">Aktivitas</a></li>
    <li class="nav-item"><a class="nav-link active" href="<?= base_url('admin/knowledge/karyawan') ?>">Master Karyawan</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge/rekap') ?>">Rekap/Leaderboard</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge/target') ?>">Target Tahunan</a></li>
</ul>
<p class="text-muted small">Data ini diambil dari sheet <code>dim_karyawan</code>. Import ulang akan MENIMPA seluruh data lama (snapshot master terbaru). Kolom ke-10 (setelah Bidang) dibaca sebagai <strong>Band</strong> (misal: Band II, Band III, dst) &mdash; dipakai untuk chart Participation Rate per Band di Dashboard Summary Inovasi.</p>
<form method="get" action="<?= base_url('admin/knowledge/karyawan') ?>" class="row g-2 mb-3">
    <div class="col-md-4"><input type="text" name="keyword" class="form-control" placeholder="Cari nama / NIK / NIP" value="<?= esc($filters['keyword'] ?? '') ?>"></div>
    <div class="col-md-4">
        <select name="departemen" class="form-select">
            <option value="">Semua Departemen</option>
            <?php foreach ($departemen as $d): ?>
                <option value="<?= esc($d) ?>" <?= ($filters['departemen'] ?? '') === $d ? 'selected' : '' ?>><?= esc($d) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="band" class="form-select">
            <option value="">Semua Band</option>
            <?php foreach (['I', 'II', 'III', 'IV', 'V'] as $b): ?>
                <option value="<?= $b ?>" <?= ($filters['band'] ?? '') === $b ? 'selected' : '' ?>>Band <?= $b ?></option>
            <?php endforeach; ?>
            <option value="-" <?= ($filters['band'] ?? '') === '-' ? 'selected' : '' ?>>Belum ada Band</option>
        </select>
    </div>
    <div class="col-md-2 d-flex gap-1">
        <button class="btn btn-secondary flex-fill">Cari</button>
        <?php if (array_filter($filters ?? [])): ?><a href="<?= base_url('admin/knowledge/karyawan') ?>" class="btn btn-outline-secondary">Reset</a><?php endif; ?>
    </div>
</form>
<p class="small text-muted"><?= number_format($total ?? 0) ?> karyawan<?= array_filter($filters ?? []) ? ' ditemukan' : '' ?>.</p>
<div class="table-responsive">
<table class="table table-striped table-sm">
    <thead><tr><th>NIK</th><th>NIP</th><th>Nama</th><th>Direktorat</th><th>Departemen</th><th>Unit Kerja</th><th>Bidang</th><th>Band</th><th style="width:130px">Aksi</th></tr></thead>
    <tbody>
    <?php if (empty($karyawan)): ?><tr><td colspan="9" class="text-center text-muted py-4">Tidak ada karyawan yang cocok.</td></tr><?php endif; ?>
    <?php foreach ($karyawan as $k): ?>
        <tr>
            <td><?= esc($k['perner']) ?></td><td><?= esc($k['id_number']) ?></td><td><?= esc($k['personnel_number']) ?></td>
            <td class="small"><?= esc($k['direktorat']) ?></td><td class="small"><?= esc($k['departemen']) ?></td>
            <td class="small"><?= esc($k['nama_unit_kerja']) ?></td><td class="small"><?= esc($k['bidang']) ?></td>
            <td class="small"><?= esc($k['band'] ?? '-') ?></td>
            <td>
                <a href="<?= base_url('admin/knowledge/karyawan/' . $k['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="<?= base_url('admin/knowledge/karyawan/' . $k['id'] . '/hapus') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus karyawan ini?')">
                    <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?= $pager->links('karyawan', 'pager/custom') ?>
<?= $this->endSection() ?>
