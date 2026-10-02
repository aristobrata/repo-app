<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Target Poin KM Tahunan</h4>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge') ?>">Aktivitas</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge/karyawan') ?>">Master Karyawan</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/knowledge/rekap') ?>">Rekap/Leaderboard</a></li>
    <li class="nav-item"><a class="nav-link active" href="<?= base_url('admin/knowledge/target') ?>">Target Tahunan</a></li>
</ul>
<p class="text-muted small">Target ini dipakai untuk menghitung kartu "% Target Tahunan" di Dashboard. Tidak ada di file Excel sumber, jadi diatur manual di sini.</p>

<form action="<?= base_url('admin/knowledge/target') ?>" method="post" class="row g-2 mb-4" style="max-width:500px;">
    <?= csrf_field() ?>
    <div class="col-md-5"><input type="text" name="tahun" class="form-control" placeholder="Tahun, mis. 2026" required></div>
    <div class="col-md-5"><input type="number" name="target_poin_tahunan" class="form-control" placeholder="Target Poin" required></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Simpan</button></div>
</form>

<table class="table table-sm" style="max-width:500px;">
    <thead><tr><th>Tahun</th><th>Target Poin</th></tr></thead>
    <tbody>
    <?php foreach ($daftarTarget as $t): ?>
        <tr><td><?= esc($t['tahun']) ?></td><td><?= number_format($t['target_poin_tahunan'], 0, ',', '.') ?></td></tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
