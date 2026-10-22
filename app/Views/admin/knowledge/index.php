<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Kelola Aktivitas Knowledge Management</h4>
    <div>
        <a href="<?= base_url('admin/knowledge/export') . (!empty($filters) ? '?' . http_build_query(array_filter($filters)) : '') ?>" class="btn btn-outline-success">📊 Export Excel</a>
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

<?php $f = fn(string $k) => $filters[$k] ?? ''; ?>
<form method="get" action="<?= base_url('admin/knowledge') ?>" class="row g-2 mb-3">
    <input type="hidden" name="per_page" value="<?= per_page_from_request('per_page') ?>">
    <div class="col-md-3"><input type="text" name="keyword" class="form-control" placeholder="Cari judul / nama / NIK / NIP" value="<?= esc($f('keyword')) ?>"></div>
    <div class="col-md-2">
        <select name="pillar" class="form-select"><option value="">Semua Pillar</option>
            <?php foreach ($opsi_pillar as $o): ?><option value="<?= esc($o) ?>" <?= $f('pillar') === $o ? 'selected' : '' ?>><?= esc($o) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="dept" class="form-select"><option value="">Semua Departemen</option>
            <?php foreach ($opsi_dept as $o): ?><option value="<?= esc($o) ?>" <?= $f('dept') === $o ? 'selected' : '' ?>><?= esc($o) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-1">
        <select name="bulan" class="form-select"><option value="">Bulan</option>
            <?php foreach ($opsi_bulan as $o): ?><option value="<?= esc($o) ?>" <?= $f('bulan') === $o ? 'selected' : '' ?>><?= esc(ucfirst(strtolower($o))) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-1">
        <select name="tahun" class="form-select"><option value="">Tahun</option>
            <?php foreach ($opsi_tahun as $o): ?><option value="<?= esc($o) ?>" <?= $f('tahun') === (string) $o ? 'selected' : '' ?>><?= esc($o) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-1">
        <select name="peran" class="form-select"><option value="">Peran</option>
            <?php foreach ($opsi_peran as $o): ?><option value="<?= esc($o) ?>" <?= $f('peran') === $o ? 'selected' : '' ?>><?= esc($o) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 d-flex gap-1">
        <button class="btn btn-secondary flex-fill">Filter</button>
        <?php if (array_filter($filters ?? [])): ?><a href="<?= base_url('admin/knowledge') ?>" class="btn btn-outline-secondary">Reset</a><?php endif; ?>
    </div>
</form>

<?= per_page_bar('aktivitas', $pager) ?>
<div class="table-responsive">
<table class="table table-striped table-sm align-middle">
    <thead><tr><th>Tanggal</th><th>Bulan</th><th>Pillar</th><th>Judul Event</th><th>Peserta</th><th>NIK</th><th>Departemen</th><th>Peran</th><th>Poin</th><th></th><th style="width:190px">Aksi</th></tr></thead>
    <tbody>
    <?php if (empty($aktivitas)): ?><tr><td colspan="11" class="text-center text-muted py-4">Tidak ada aktivitas yang cocok.</td></tr><?php endif; ?>
    <?php foreach ($aktivitas as $a): ?>
        <tr>
            <td class="small text-nowrap"><?= esc($a['tanggal_score']) ?></td>
            <td><?= esc($a['bulan']) ?></td>
            <td class="small"><?= esc($a['pillar_km']) ?></td>
            <td><?= esc($a['judul_event']) ?></td>
            <td><?= esc($a['nama_peserta']) ?></td>
            <td class="small"><?= esc($a['nik']) ?></td>
            <td class="small"><?= esc($a['departemen']) ?></td>
            <td><?= esc($a['peran']) ?></td>
            <td><?= (int) $a['poin'] ?></td>
            <td class="text-nowrap" title="Lampiran / Sampul"><?= !empty($a['file_dokumen']) ? '📎' : '' ?><?= !empty($a['cover']) ? '🖼' : '' ?></td>
            <td class="text-nowrap">
                <a href="<?= base_url('library/km/' . $a['id']) ?>" class="btn btn-sm btn-outline-secondary" target="_blank">Lihat</a>
                <a href="<?= base_url('admin/knowledge/' . $a['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="<?= base_url('admin/knowledge/' . $a['id'] . '/hapus') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus aktivitas ini beserta lampiran dan sampulnya?')">
                    <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?= $pager->links('aktivitas', 'pager/custom') ?>
<?= $this->endSection() ?>
