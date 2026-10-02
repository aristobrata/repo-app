<?= $this->extend('layouts/main') ?>
<?= $this->section('styles') ?>
<style>
    .kpi-card { padding: 16px; }
    .kpi-value { font-size: 20px; font-weight: 700; line-height: 1.0; color: #0F2027; }
    .kpi-label { font-size: 10.5px; font-weight: 600; color: var(--sig-muted); text-transform: uppercase; letter-spacing: .03em; }
    .kpi-delta-up { color: var(--sig-success); font-size: 10pt; }
    .kpi-icon { font-size: 22px; }
    .chart-card-title { font-size: 12px; font-weight: 700; color: #0F2027; margin-bottom: 12px; }
    .dash-row { margin-bottom: 16px; }
    .leaderboard-rank-gold { color: var(--sig-gold); font-weight: 700; }
    .nav-tabs .nav-link.active { border-bottom: 2px solid var(--sig-red); color: var(--sig-red); font-weight: 600; }
    .nav-tabs .nav-link { color: var(--sig-muted); font-size: 10.5pt; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- GLOBAL HEADER & FILTER BAR -->
<div class="card mb-3">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-0" style="font-size:20px; font-weight:700;">Dashboard Knowledge Management & Inovasi</h5>
            <p class="mb-0 small" style="color:var(--sig-muted);">
                Cut-off Data: <?= date('d M Y') ?> &middot; Status Sistem: <span style="color:var(--sig-success);">● Active</span>
            </p>
        </div>
        <form method="get" class="d-flex gap-2">
            <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>>Tahun <?= $y ?></option>
                <?php endfor; ?>
            </select>
        </form>
    </div>
</div>

<!-- ROW 1: KPI METRIC CARDS -->
<div class="row dash-row g-3">
    <div class="col-md-3">
        <div class="card kpi-card h-100">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="kpi-label">Total Poin KM</div>
                    <div class="kpi-value"><?= number_format($total_poin_km, 0, ',', '.') ?></div>
                    <div class="small" style="color:var(--sig-muted);">Akumulasi tahun <?= esc($tahun) ?></div>
                </div>
                <div class="kpi-icon">📈</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card h-100">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="kpi-label">% Target Tahunan</div>
                    <div class="kpi-value"><?= $pct_achievement !== null ? $pct_achievement . '%' : '-' ?></div>
                    <?php if ($target_tahunan > 0): ?>
                        <div class="progress mt-1" style="height:6px;">
                            <div class="progress-bar" style="width: <?= min(100, $pct_achievement) ?>%; background-color: var(--sig-red);"></div>
                        </div>
                    <?php else: ?>
                        <div class="small" style="color:var(--sig-muted);">Target belum diatur</div>
                    <?php endif; ?>
                </div>
                <div class="kpi-icon">🎯</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card h-100">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="kpi-label">Partisipasi Karyawan</div>
                    <div class="kpi-value"><?= $pct_partisipasi !== null ? $pct_partisipasi . '%' : '-' ?></div>
                    <div class="small" style="color:var(--sig-muted);"><?= $jumlah_peserta_aktif ?> dari <?= $total_karyawan ?> karyawan</div>
                </div>
                <div class="kpi-icon">👥</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card h-100">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="kpi-label">Total Inovasi</div>
                    <div class="kpi-value"><?= $total_inovasi_tahun ?></div>
                    <div class="small" style="color:var(--sig-muted);">Terdaftar tahun <?= esc($tahun) ?></div>
                </div>
                <div class="kpi-icon">💡</div>
            </div>
        </div>
    </div>
</div>

<!-- ROW 2: TREN & DISTRIBUSI UTAMA -->
<div class="row dash-row g-3">
    <div class="col-md-7">
        <div class="card kpi-card h-100">
            <div class="chart-card-title">Tren Poin Bulanan <?= esc($tahun) ?></div>
            <canvas id="chartTren" height="90"></canvas>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card kpi-card h-100">
            <div class="chart-card-title">Poin per Pilar KM</div>
            <canvas id="chartPilar" height="90"></canvas>
        </div>
    </div>
</div>

<!-- ROW 3: DETAIL DISTRIBUSI & LEADERBOARD -->
<div class="row dash-row g-3">
    <div class="col-md-4">
        <div class="card kpi-card h-100">
            <div class="chart-card-title">Inovasi per Departemen</div>
            <canvas id="chartDept" height="110"></canvas>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card h-100">
            <div class="chart-card-title">Keterlibatan per Band</div>
            <canvas id="chartBand" height="110"></canvas>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card kpi-card h-100">
            <div class="chart-card-title">Leaderboard</div>
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabKaryawan">Top Karyawan</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabDept">Top Departemen</button></li>
            </ul>
            <div class="tab-content pt-2">
                <div class="tab-pane fade show active" id="tabKaryawan">
                    <table class="table table-sm mb-0">
                        <tbody>
                        <?php foreach ($top_karyawan as $i => $k): ?>
                            <tr>
                                <td class="<?= $i < 3 ? 'leaderboard-rank-gold' : '' ?>" style="width:24px;"><?= $i + 1 ?></td>
                                <td><?= esc($k['nama']) ?><br><span class="small" style="color:var(--sig-muted);"><?= esc($k['departemen']) ?></span></td>
                                <td class="text-end fw-bold"><?= number_format($k['total_poin'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($top_karyawan)): ?><tr><td class="text-muted small">Belum ada data rekap. Import dulu di Admin &rarr; Rekap/Leaderboard.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="tabDept">
                    <table class="table table-sm mb-0">
                        <tbody>
                        <?php foreach ($top_departemen as $i => $d): ?>
                            <tr>
                                <td class="<?= $i < 3 ? 'leaderboard-rank-gold' : '' ?>" style="width:24px;"><?= $i + 1 ?></td>
                                <td><?= esc($d['departemen']) ?><br><span class="small" style="color:var(--sig-muted);"><?= $d['jumlah_karyawan'] ?> karyawan</span></td>
                                <td class="text-end fw-bold"><?= number_format($d['total_poin'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($top_departemen)): ?><tr><td class="text-muted small">Belum ada data.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (in_array(session()->get('role'), ['admin', 'super_admin'])): ?>
<div class="card kpi-card">
    <div class="chart-card-title">Aktivitas Sistem Terbaru</div>
    <table class="table table-sm mb-0">
        <tbody>
        <?php foreach ($aktivitas_terbaru as $a): ?>
            <tr><td class="small text-muted"><?= esc($a['created_at']) ?></td><td class="small"><?= esc($a['nama_user'] ?? '-') ?></td><td class="small"><?= esc($a['action']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = 'Inter';
const SIG_RED = '#C8102E', SIG_BLUE = '#1E3A8A', SIG_GOLD = '#D97706', SIG_GREEN = '#10B981';

// CHT-01: Tren Poin Bulanan
const tren = <?= json_encode($tren_poin_bulanan) ?>;
new Chart(document.getElementById('chartTren'), {
    type: 'line',
    data: { labels: tren.map(t => t.bulan), datasets: [{
        label: 'Poin Aktual', data: tren.map(t => t.total_poin),
        borderColor: SIG_BLUE, backgroundColor: 'transparent', tension: 0.3,
    }]},
    options: { plugins: { legend: { display: false } } }
});

// CHT-02: Poin per Pilar KM
const pilar = <?= json_encode($poin_per_pilar) ?>;
new Chart(document.getElementById('chartPilar'), {
    type: 'bar',
    data: { labels: pilar.map(p => p.pillar_km), datasets: [{ data: pilar.map(p => p.total_poin), backgroundColor: SIG_RED }] },
    options: { plugins: { legend: { display: false } }, indexAxis: 'y' }
});

// CHT-03: Inovasi per Dept
const dept = <?= json_encode($inovasi_per_dept) ?>;
new Chart(document.getElementById('chartDept'), {
    type: 'bar',
    data: { labels: dept.map(d => d.dept), datasets: [{ data: dept.map(d => d.total), backgroundColor: SIG_BLUE }] },
    options: { plugins: { legend: { display: false } } }
});

// CHT-04: Keterlibatan per Band (donut)
const band = <?= json_encode($keterlibatan_band) ?>;
new Chart(document.getElementById('chartBand'), {
    type: 'doughnut',
    data: { labels: band.map(b => 'Band ' + b.band), datasets: [{ data: band.map(b => b.total), backgroundColor: [SIG_RED, SIG_BLUE, SIG_GOLD, SIG_GREEN, '#64748B'] }] },
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
