<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($title ?? 'Summary Inovasi') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
  // Palet warna diambil dari logo PT Semen Padang (merah #D73338 & hitam/charcoal #272727),
  // ditambah emas (#D97706) sebagai aksen ketiga -- sama seperti warna "gold" yang sudah
  // dipakai di bagian lain aplikasi ini (lihat --sig-gold di app/Views/layouts/main.php).
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: { sans: ['Inter', 'sans-serif'] },
        colors: {
          brand: { DEFAULT: '#D73338', dark: '#A8262A', light: '#E8696D' },
          ink: { DEFAULT: '#272727', muted: '#5B5B5B', light: '#9CA3AF' },
          gold: { DEFAULT: '#D97706', light: '#F2A33D' },
        },
      },
    },
  };
</script>
<style>
  body { background: #f3f4f6; }
  .card-soft { box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 4px 10px rgba(15,23,42,.04); }
  .topbar-gradient { background: linear-gradient(90deg, #272727, #D73338); }
</style>
</head>
<body class="font-sans text-ink">

<?php
// Angka bantu yang dipakai berulang di halaman ini.
$belumTerlibat = max(0, $total_karyawan - $karyawan_terlibat);
?>

<div class="h-1.5 topbar-gradient"></div>

<div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between flex-wrap gap-2">
    <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center gap-1 text-sm text-ink-muted hover:text-brand">
        &larr; Kembali ke Dashboard
    </a>
    <form method="get" class="flex items-center gap-2 text-sm">
        <label for="tahun" class="text-ink-muted">Tahun Inovasi:</label>
        <select id="tahun" name="tahun" onchange="this.form.submit()" class="border border-slate-300 rounded-lg px-2 py-1 text-sm">
            <option value="">Semua Tahun</option>
            <?php foreach ($daftar_tahun as $y): ?>
                <option value="<?= esc($y) ?>" <?= (string) $tahun === (string) $y ? 'selected' : '' ?>><?= esc($y) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="max-w-7xl mx-auto px-4 pb-12">

    <!-- HEADER -->
    <header class="flex items-center justify-between gap-4 bg-white rounded-2xl card-soft border-t-4 border-brand px-6 py-5 mb-6">
        <div class="w-24 h-14 flex items-center justify-center rounded-lg bg-slate-100 border border-slate-200 text-slate-400 text-xs font-bold tracking-wide shrink-0">
            LOGO SIG
        </div>
        <div class="text-center flex-1">
            <h1 class="text-2xl md:text-3xl font-extrabold uppercase tracking-tight text-ink">Dashboard / Summary Inovasi</h1>
            <p class="text-sm text-ink-muted mt-1">Data s/d <?= esc($periode) ?> &middot; <?= $tahun ? 'Tahun ' . esc($tahun) : 'Semua Tahun' ?></p>
        </div>
        <div class="w-24 h-14 flex items-center justify-center shrink-0">
            <img src="<?= base_url('assets/img/logo-semen-padang.png') ?>" alt="Semen Padang" class="max-h-14 w-auto">
        </div>
    </header>

    <!-- TOP METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

        <div class="bg-white rounded-2xl card-soft border-l-4 border-brand p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-ink-muted mb-2">Total Inovasi</p>
            <p class="text-3xl font-bold text-ink"><?= number_format($total_inovasi, 0, ',', '.') ?></p>
            <p class="text-xs text-ink-muted mt-1"><?= $tahun ? 'Terdaftar tahun ' . esc($tahun) : 'Terdaftar, semua tahun' ?></p>
        </div>

        <div class="bg-white rounded-2xl card-soft border-l-4 border-ink p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-ink-muted mb-2">Kategori Terbanyak</p>
            <p class="text-3xl font-bold text-ink"><?= $top_kategori ? number_format($top_kategori['total'], 0, ',', '.') : '-' ?></p>
            <p class="text-xs text-ink-muted mt-1"><?= $top_kategori ? esc($top_kategori['kategori']) : 'Belum ada data' ?></p>
        </div>

        <div class="bg-white rounded-2xl card-soft border-l-4 border-brand-light p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-ink-muted mb-2">Kategori Aktif</p>
            <p class="text-3xl font-bold text-ink"><?= number_format($jumlah_kategori_aktif, 0, ',', '.') ?></p>
            <p class="text-xs text-ink-muted mt-1">jenis kategori inovasi dipakai</p>
        </div>

        <div class="bg-white rounded-2xl card-soft border-l-4 border-gold p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-ink-muted mb-2">Departemen Pengusul</p>
            <p class="text-3xl font-bold text-ink"><?= number_format($cakupan_dept, 0, ',', '.') ?></p>
            <p class="text-xs text-ink-muted mt-1">fungsi/departemen unik</p>
        </div>

        <div class="bg-white rounded-2xl card-soft border-l-4 border-gold-light p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-ink-muted mb-2">Karyawan Terlibat KM</p>
            <p class="text-3xl font-bold text-ink"><?= number_format($karyawan_terlibat, 0, ',', '.') ?></p>
            <p class="text-xs text-ink-muted mt-1"><?= number_format($pct_terlibat, 1, ',', '.') ?>% dari <?= number_format($total_karyawan, 0, ',', '.') ?> karyawan (akumulasi)</p>
        </div>

    </div>

    <!-- MAIN BODY: 2 KOLOM -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KOLOM KIRI (2/3) -->
        <div class="lg:col-span-2 flex flex-col gap-6">

            <!-- Section 1: Persentase Keterlibatan -->
            <div class="bg-white rounded-2xl card-soft p-6">
                <h2 class="text-sm font-bold text-ink mb-1">Persentase Keterlibatan Karyawan (KM)</h2>
                <p class="text-xs text-ink-muted mb-3">Akumulasi seluruh waktu -- tidak mengikuti filter tahun di atas.</p>
                <div class="w-full h-5 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full bg-brand" style="width:<?= min(100, $pct_terlibat) ?>%"></div>
                </div>
                <div class="flex justify-between mt-2 text-sm">
                    <span class="font-semibold text-brand"><?= number_format($karyawan_terlibat, 0, ',', '.') ?> terlibat</span>
                    <span class="text-ink-muted"><?= number_format($belumTerlibat, 0, ',', '.') ?> belum tercatat terlibat</span>
                </div>
            </div>

            <!-- Section 2: Inovasi per Kategori (data ASLI dari database) -->
            <div class="bg-white rounded-2xl card-soft p-6">
                <h2 class="text-sm font-bold text-ink mb-1">Inovasi per Kategori</h2>
                <p class="text-xs text-ink-muted mb-3">Kategori sesuai data asli di tabel inovasi Anda (bukan daftar baku) &middot; <?= $tahun ? 'tahun ' . esc($tahun) : 'semua tahun' ?></p>
                <?php if (empty($kategori_breakdown)): ?>
                    <p class="text-xs text-ink-muted">Belum ada data inovasi untuk filter ini.</p>
                <?php else: ?>
                    <div style="height:<?= max(180, count($kategori_breakdown) * 32) ?>px;">
                        <canvas id="kategoriChart"></canvas>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Section 3: Participation Rate per Band -->
            <div class="bg-white rounded-2xl card-soft p-6">
                <h2 class="text-sm font-bold text-ink mb-1">Participation Rate per Band</h2>
                <p class="text-xs text-ink-muted mb-3">Akumulasi seluruh waktu.</p>
                <?php if (empty($band_data)): ?>
                    <p class="text-xs text-ink-muted">Belum ada data Band di master karyawan. Isi kolom <strong>Band</strong> lewat Import Excel Master Karyawan (Admin &rarr; Kelola Aktivitas KM &rarr; Master Karyawan) supaya chart ini terisi otomatis.</p>
                <?php else: ?>
                    <div style="height:220px;">
                        <canvas id="bandChart"></canvas>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-4 text-xs">
                        <?php foreach ($band_data as $bd): ?>
                            <div class="text-center">
                                <span class="font-bold text-ink"><?= number_format($bd['pct'], 1, ',', '.') ?>%</span>
                                <span class="text-ink-muted"> | <?= $bd['terlibat'] ?>/<?= $bd['total'] ?></span>
                                <div class="text-ink-muted">Band <?= esc($bd['band']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Section 4: Catatan Kaki Kiri -->
            <div class="bg-white rounded-2xl card-soft p-6">
                <p class="text-lg font-extrabold italic text-brand mb-3">"Go Beyond Next"</p>
                <ul class="list-disc list-inside text-xs text-ink-muted space-y-1">
                    <li>Band I diharapkan menjadi sponsor, enabler, pengarah inovasi</li>
                    <li>Angka karyawan terlibat hanya menghitung karyawan internal yang tercatat di aktivitas KM.</li>
                </ul>
            </div>

        </div>

        <!-- KOLOM KANAN (1/3): KETERANGAN -->
        <div class="bg-white rounded-2xl card-soft p-6 h-fit">
            <h2 class="text-sm font-bold text-ink mb-5">Keterangan</h2>

            <div class="flex gap-3 mb-5">
                <span class="shrink-0 w-7 h-7 rounded-full bg-ink text-white text-sm font-bold flex items-center justify-center">1</span>
                <div>
                    <p class="text-sm font-semibold text-ink">Kategori terbanyak</p>
                    <p class="text-xs text-ink-muted mt-1">
                        <?php if ($top_kategori): ?>
                            Kategori <strong><?= esc($top_kategori['kategori']) ?></strong> adalah yang paling banyak dipakai dengan <?= number_format($top_kategori['total'], 0, ',', '.') ?> inovasi<?= $tahun ? ' pada tahun ' . esc($tahun) : '' ?>. Ini jadi acuan kategori mana yang paling aktif diajukan karyawan.
                        <?php else: ?>
                            Belum ada data inovasi untuk filter tahun yang dipilih.
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <div class="flex gap-3 mb-5">
                <span class="shrink-0 w-7 h-7 rounded-full bg-gold text-white text-sm font-bold flex items-center justify-center">2</span>
                <div>
                    <p class="text-sm font-semibold text-ink">Keragaman kategori</p>
                    <p class="text-xs text-ink-muted mt-1"><?= number_format($jumlah_kategori_aktif, 0, ',', '.') ?> kategori berbeda dipakai dari <?= number_format($cakupan_dept, 0, ',', '.') ?> departemen/fungsi pengusul<?= $tahun ? ' pada tahun ' . esc($tahun) : '' ?>, menunjukkan seberapa luas program improvement tersebar di perusahaan.</p>
                </div>
            </div>

            <div class="flex gap-3">
                <span class="shrink-0 w-7 h-7 rounded-full bg-brand text-white text-sm font-bold flex items-center justify-center">3</span>
                <div>
                    <p class="text-sm font-semibold text-ink">Partisipasi = breadth indicator</p>
                    <p class="text-xs text-ink-muted mt-1"><?= number_format($karyawan_terlibat, 0, ',', '.') ?> karyawan internal (<?= number_format($pct_terlibat, 1, ',', '.') ?>%) sudah terlibat dalam aktivitas Knowledge Management. Tantangannya adalah memperluas basis peserta.</p>
                </div>
            </div>
        </div>

    </div>

    <!-- FOOTER -->
    <footer class="flex justify-end mt-8">
        <p class="text-sm md:text-base font-extrabold uppercase tracking-wide text-ink text-right">
        </p>
    </footer>

</div>

<?php if (!empty($kategori_breakdown)): ?>
<script>
new Chart(document.getElementById('kategoriChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($kategori_breakdown, 'kategori')) ?>,
        datasets: [{
            label: 'Jumlah Inovasi',
            data: <?= json_encode(array_map('intval', array_column($kategori_breakdown, 'total'))) ?>,
            backgroundColor: '#D73338',
            borderRadius: 6,
            barThickness: 18,
        }],
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#e2e8f0' } },
            y: { grid: { display: false } },
        },
    },
});
</script>
<?php endif; ?>

<?php if (!empty($band_data)): ?>
<script>
new Chart(document.getElementById('bandChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_map(static fn ($b) => 'Band ' . $b['band'], $band_data)) ?>,
        datasets: [{
            label: 'Participation Rate',
            data: <?= json_encode(array_column($band_data, 'pct')) ?>,
            backgroundColor: ['#D73338', '#272727', '#D97706', '#A8262A', '#5B5B5B', '#F2A33D'],
            borderRadius: 6,
            barThickness: 24,
        }],
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function (item) {
                        const row = <?= json_encode($band_data) ?>[item.dataIndex];
                        return row.pct + '% (' + row.terlibat + '/' + row.total + ')';
                    },
                },
            },
        },
        scales: {
            x: { beginAtZero: true, ticks: { callback: (v) => v + '%' }, grid: { color: '#e2e8f0' } },
            y: { grid: { display: false } },
        },
    },
});
</script>
<?php endif; ?>

</body>
</html>
