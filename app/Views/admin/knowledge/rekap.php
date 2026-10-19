<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Rekap Poin & Leaderboard Karyawan</h4>
    <a href="<?= base_url('admin/knowledge/rekap/export') . '?' . http_build_query(array_filter($filters ?? [])) ?>" class="btn btn-outline-success">📊 Export Excel</a>
</div>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge') ?>">Aktivitas</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge/karyawan') ?>">Master Karyawan</a></li>
    <li class="nav-item"><a class="nav-link active" href="<?= base_url('admin/knowledge/rekap') ?>">Rekap/Leaderboard</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge/target') ?>">Target Tahunan</a></li>
</ul>
<p class="text-muted small">Data ini yang dipakai Leaderboard di Dashboard. Import ulang akan MENIMPA seluruh rekap lama.</p>
<form method="get" action="<?= base_url('admin/knowledge/rekap') ?>" class="row g-2 mb-3">
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
        </select>
    </div>
    <div class="col-md-2 d-flex gap-1">
        <button class="btn btn-secondary flex-fill">Cari</button>
        <?php if (array_filter($filters ?? [])): ?><a href="<?= base_url('admin/knowledge/rekap') ?>" class="btn btn-outline-secondary">Reset</a><?php endif; ?>
    </div>
</form>
<p class="small text-muted"><?= number_format($total ?? 0) ?> karyawan<?= array_filter($filters ?? []) ? ' ditemukan' : '' ?>.</p>
<div class="table-responsive">
<table class="table table-striped table-sm">
    <thead><tr><th>Rank</th><th>NIK</th><th>Nama</th><th>Departemen</th><th>Unit</th><th>Total Poin</th><th>Band</th></tr></thead>
    <tbody>
    <?php if (empty($rekap)): ?><tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data yang cocok.</td></tr><?php endif; ?>
    <?php foreach ($rekap as $r): ?>
        <tr>
            <td><?= (int) $r['rank'] ?></td><td><?= esc($r['nik']) ?></td><td><?= esc($r['nama']) ?></td>
            <td class="small"><?= esc($r['departemen']) ?></td><td class="small"><?= esc($r['unit']) ?></td>
            <td><strong><?= number_format($r['total_poin'], 0, ',', '.') ?></strong></td>
            <td><span class="badge bg-secondary">Band <?= esc($r['band']) ?></span></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?= $pager->links('rekap', 'pager/custom') ?>
<?= $this->endSection() ?>
