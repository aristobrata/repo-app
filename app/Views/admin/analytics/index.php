<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-4">Analytics Repository Dokumen</h4>
<p class="text-muted small">Analytics untuk Inovasi & Knowledge Management ada di halaman Dashboard utama.</p>

<div class="row mb-4">
    <div class="col-md-4"><div class="card text-center"><div class="card-body"><h2><?= (int) $total_dokumen ?></h2><p class="text-muted mb-0">Total Dokumen</p></div></div></div>
</div>

<div class="row mb-4">
    <div class="col-md-6"><h6>Dokumen per Kategori</h6><canvas id="chartKategori"></canvas></div>
    <div class="col-md-6"><h6>Tren Aktivitas Baca Dokumen (30 hari)</h6><canvas id="chartTren"></canvas></div>
</div>

<h6>Top 10 Dokumen Paling Dilihat</h6>
<ol><?php foreach ($dokumen_populer as $d): ?><li><?= esc($d['judul']) ?> — <?= (int) $d['jumlah_view'] ?> views</li><?php endforeach; ?></ol>

<h6>Log Aktivitas Terbaru</h6>
<table class="table table-sm">
    <thead><tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Target</th></tr></thead>
    <tbody>
    <?php foreach ($log_aktivitas as $l): ?>
        <tr><td><?= esc($l['created_at']) ?></td><td><?= esc($l['nama_user'] ?? '-') ?></td><td><?= esc($l['action']) ?></td><td><?= esc($l['keterangan'] ?? '-') ?></td></tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const dataKategori = <?= json_encode($dokumen_per_kategori) ?>;
new Chart(document.getElementById('chartKategori'), { type: 'pie', data: { labels: dataKategori.map(d => d.kategori), datasets: [{ data: dataKategori.map(d => d.total) }] } });
const dataTren = <?= json_encode($tren_harian) ?>;
new Chart(document.getElementById('chartTren'), { type: 'line', data: { labels: dataTren.map(d => d.tanggal), datasets: [{ label: 'Jumlah Dibaca', data: dataTren.map(d => d.total) }] } });
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
