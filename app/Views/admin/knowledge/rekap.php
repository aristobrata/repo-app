<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Rekap Poin & Leaderboard Karyawan</h4>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge') ?>">Aktivitas</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge/karyawan') ?>">Master Karyawan</a></li>
    <li class="nav-item"><a class="nav-link active" href="<?= base_url('admin/knowledge/rekap') ?>">Rekap/Leaderboard</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge/target') ?>">Target Tahunan</a></li>
</ul>
<p class="text-muted small">Data ini yang dipakai Leaderboard di Dashboard. Import ulang akan MENIMPA seluruh rekap lama.</p>
<div class="table-responsive">
<table class="table table-striped table-sm">
    <thead><tr><th>Rank</th><th>NIK</th><th>Nama</th><th>Departemen</th><th>Unit</th><th>Total Poin</th><th>Band</th></tr></thead>
    <tbody>
    <?php $no = 1; foreach ($rekap as $r): ?>
        <tr>
            <td><?= $no++ ?></td><td><?= esc($r['nik']) ?></td><td><?= esc($r['nama']) ?></td>
            <td class="small"><?= esc($r['departemen']) ?></td><td class="small"><?= esc($r['unit']) ?></td>
            <td><strong><?= number_format($r['total_poin'], 0, ',', '.') ?></strong></td>
            <td><span class="badge bg-secondary">Band <?= esc($r['band']) ?></span></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?= $pager->links('rekap', 'default_full') ?>
<?= $this->endSection() ?>
