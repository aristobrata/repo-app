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
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: { sans: ['Inter', 'sans-serif'] },
        colors: {
          teal: { DEFAULT: '#0d9488', light: '#14b8a6' },
          navy: { DEFAULT: '#1e40af', light: '#0284c7' },
          orange: { DEFAULT: '#ea580c', light: '#f97316', soft: '#fbbf24' },
          ink: { DEFAULT: '#0f172a', muted: '#475569' },
        },
      },
    },
  };
</script>
<style>
  body { background: #f3f4f6; }
  .card-soft { box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 4px 10px rgba(15,23,42,.04); }
</style>
</head>
<body class="font-sans text-ink">

<div class="max-w-7xl mx-auto px-4 py-3">
    <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center gap-1 text-sm text-ink-muted hover:text-navy">
        &larr; Kembali ke Dashboard
    </a>
</div>

<div class="max-w-7xl mx-auto px-4 pb-12">

    <!-- HEADER -->
    <header class="flex items-center justify-between gap-4 bg-white rounded-2xl card-soft px-6 py-5 mb-6">
        <div class="w-24 h-14 flex items-center justify-center rounded-lg bg-slate-100 border border-slate-200 text-slate-400 text-xs font-bold tracking-wide shrink-0">
            LOGO SIG
        </div>
        <div class="text-center flex-1">
            <h1 class="text-2xl md:text-3xl font-extrabold uppercase tracking-tight text-ink">Dashboard / Summary Inovasi</h1>
            <p class="text-sm text-ink-muted mt-1">Data s/d <?= esc($periode) ?></p>
        </div>
        <div class="w-24 h-14 flex items-center justify-center rounded-lg bg-slate-100 border border-slate-200 text-slate-400 text-xs font-bold tracking-wide shrink-0 text-center leading-tight">
            LOGO SEMEN<br>PADANG
        </div>
    </header>

    <!-- TOP METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

        <div class="bg-white rounded-2xl card-soft border-l-4 border-teal-light p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-ink-muted mb-2">Karyawan Terlibat</p>
            <p class="text-3xl font-bold text-ink">254</p>
            <p class="text-xs text-ink-muted mt-1">21,1% dari 1.204 karyawan</p>
        </div>

        <div class="bg-white rounded-2xl card-soft border-l-4 border-navy p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-ink-muted mb-2">Total Source</p>
            <p class="text-3xl font-bold text-ink">174</p>
            <p class="text-xs text-ink-muted mt-1">159 FI + 15 RI</p>
        </div>

        <div class="bg-white rounded-2xl card-soft border-l-4 border-navy-light p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-ink-muted mb-2">Focus Improvement (FI)</p>
            <p class="text-3xl font-bold text-ink">159</p>
            <p class="text-xs text-ink-muted mt-1">khusus Direktorat Operasional</p>
        </div>

        <div class="bg-white rounded-2xl card-soft border-l-4 border-orange p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-ink-muted mb-2">Registrasi Inovasi (RI)</p>
            <p class="text-3xl font-bold text-ink">15</p>
            <p class="text-xs text-ink-muted mt-1">terimplementasi &amp; siap diuji</p>
        </div>

        <div class="bg-white rounded-2xl card-soft border-l-4 border-orange-soft p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-ink-muted mb-2">Cakupan Registrasi Inovasi</p>
            <p class="text-3xl font-bold text-ink">7</p>
            <p class="text-xs text-ink-muted mt-1">fungsi/departemen pengusul</p>
        </div>

    </div>

    <!-- MAIN BODY: 2 KOLOM -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KOLOM KIRI (2/3) -->
        <div class="lg:col-span-2 flex flex-col gap-6">

            <!-- Section 1: Persentase Keterlibatan -->
            <div class="bg-white rounded-2xl card-soft p-6">
                <h2 class="text-sm font-bold text-ink mb-4">Persentase Keterlibatan Karyawan</h2>
                <div class="w-full h-5 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full bg-teal-light" style="width:21.1%"></div>
                </div>
                <div class="flex justify-between mt-2 text-sm">
                    <span class="font-semibold text-teal">254 terlibat</span>
                    <span class="text-ink-muted">950 belum tercatat terlibat</span>
                </div>
            </div>

            <!-- Section 2: Participation Rate per Band -->
            <div class="bg-white rounded-2xl card-soft p-6">
                <h2 class="text-sm font-bold text-ink mb-4">Participation Rate per Band</h2>
                <div style="height:220px;">
                    <canvas id="bandChart"></canvas>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-4 text-xs">
                    <div class="text-center"><span class="font-bold text-ink">11,0%</span><span class="text-ink-muted"> | 9/82</span><div class="text-ink-muted">Band II</div></div>
                    <div class="text-center"><span class="font-bold text-ink">16,5%</span><span class="text-ink-muted"> | 30/182</span><div class="text-ink-muted">Band III</div></div>
                    <div class="text-center"><span class="font-bold text-ink">24,9%</span><span class="text-ink-muted"> | 160/642</span><div class="text-ink-muted">Band IV</div></div>
                    <div class="text-center"><span class="font-bold text-ink">19,6%</span><span class="text-ink-muted"> | 55/281</span><div class="text-ink-muted">Band V</div></div>
                </div>
            </div>

            <!-- Section 3: Catatan Kaki Kiri -->
            <div class="bg-white rounded-2xl card-soft p-6">
                <p class="text-lg font-extrabold italic text-navy mb-3">"Go Beyond Next"</p>
                <ul class="list-disc list-inside text-xs text-ink-muted space-y-1">
                    <li>Band 1 diharapkan menjadi sponsor, enabler, pengarah inovasi</li>
                    <li>10 tenaga OS juga tercatat sebagai peserta; angka 254 hanya karyawan internal.</li>
                </ul>
            </div>

        </div>

        <!-- KOLOM KANAN (1/3): KETERANGAN -->
        <div class="bg-white rounded-2xl card-soft p-6 h-fit">
            <h2 class="text-sm font-bold text-ink mb-5">Keterangan</h2>

            <div class="flex gap-3 mb-5">
                <span class="shrink-0 w-7 h-7 rounded-full bg-navy text-white text-sm font-bold flex items-center justify-center">1</span>
                <div>
                    <p class="text-sm font-semibold text-ink">FI = penggerak improvement</p>
                    <p class="text-xs text-ink-muted mt-1">159 FI menunjukkan pipeline continuous improvement yang aktif di Dir. Operasional. Sebagian masih berupa improvement kecil; kualitas/impact perlu menjadi fokus berikutnya.</p>
                </div>
            </div>

            <div class="flex gap-3 mb-5">
                <span class="shrink-0 w-7 h-7 rounded-full bg-orange text-white text-sm font-bold flex items-center justify-center">2</span>
                <div>
                    <p class="text-sm font-semibold text-ink">RI = innovation-ready pipeline</p>
                    <p class="text-xs text-ink-muted mt-1">15 RI sudah terimplementasi dan siap diuji pada penilaian akhir tahun. Karena terbuka untuk seluruh fungsi, RI menjadi kanal utama untuk memperluas inovasi lintas perusahaan.</p>
                </div>
            </div>

            <div class="flex gap-3">
                <span class="shrink-0 w-7 h-7 rounded-full bg-teal text-white text-sm font-bold flex items-center justify-center">3</span>
                <div>
                    <p class="text-sm font-semibold text-ink">Partisipasi = breadth indicator</p>
                    <p class="text-xs text-ink-muted mt-1">254 karyawan internal (21,1%) sudah terlibat dalam ekosistem inovasi. Tantangannya adalah memperluas basis peserta.</p>
                </div>
            </div>
        </div>

    </div>

    <!-- FOOTER -->
    <footer class="flex justify-end mt-8">
        <p class="text-sm md:text-base font-extrabold uppercase tracking-wide text-ink text-right">
            Kami Telah Berbuat Sebelum Yang Lain Memikirkannya
        </p>
    </footer>

</div>

<script>
const ctx = document.getElementById('bandChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Band II', 'Band III', 'Band IV', 'Band V'],
        datasets: [{
            label: 'Participation Rate',
            data: [11.0, 16.5, 24.9, 19.6],
            backgroundColor: ['#0d9488', '#14b8a6', '#0284c7', '#1e40af'],
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
                        const detail = ['9/82', '30/182', '160/642', '55/281'][item.dataIndex];
                        return item.parsed.x + '% (' + detail + ')';
                    },
                },
            },
        },
        scales: {
            x: { beginAtZero: true, max: 30, ticks: { callback: (v) => v + '%' }, grid: { color: '#e2e8f0' } },
            y: { grid: { display: false } },
        },
    },
});
</script>

</body>
</html>
