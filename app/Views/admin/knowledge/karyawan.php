<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Master Data Karyawan (Knowledge Management)</h4>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge') ?>">Aktivitas</a></li>
    <li class="nav-item"><a class="nav-link active" href="<?= base_url('admin/knowledge/karyawan') ?>">Master Karyawan</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge/rekap') ?>">Rekap/Leaderboard</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge/target') ?>">Target Tahunan</a></li>
</ul>
<p class="text-muted small">Data ini diambil dari sheet <code>dim_karyawan</code>. Import ulang akan MENIMPA seluruh data lama (snapshot master terbaru).</p>
<div class="table-responsive">
<table class="table table-striped table-sm">
    <thead><tr><th>NIK</th><th>NIP</th><th>Nama</th><th>Direktorat</th><th>Departemen</th><th>Unit Kerja</th><th>Bidang</th></tr></thead>
    <tbody>
    <?php foreach ($karyawan as $k): ?>
        <tr>
            <td><?= esc($k['perner']) ?></td><td><?= esc($k['id_number']) ?></td><td><?= esc($k['personnel_number']) ?></td>
            <td class="small"><?= esc($k['direktorat']) ?></td><td class="small"><?= esc($k['departemen']) ?></td>
            <td class="small"><?= esc($k['nama_unit_kerja']) ?></td><td class="small"><?= esc($k['bidang']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?= $pager->links('karyawan', 'default_full') ?>
<?= $this->endSection() ?>
