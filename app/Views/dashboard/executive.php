<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Eksekutif</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
  tailwind.config = { theme: { extend: {
    fontFamily: { sans: ['Inter', 'sans-serif'] },
    colors: { brand: { DEFAULT: '#D73338', dark: '#A8262A', light: '#E8696D' },
              ink: { DEFAULT: '#272727', muted: '#5B5B5B', light: '#9CA3AF' }, gold: { DEFAULT: '#D97706' } } } } };
</script>
<style>
  html { scroll-behavior: smooth; scroll-padding-top: 64px; }
  body { background: #f3f4f6; }
  .card { background:#fff; border-radius:1rem; box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 4px 10px rgba(15,23,42,.04); }
  .topbar-gradient { background: linear-gradient(90deg, #272727, #D73338); }
  .chart-box { position: relative; height: 280px; }
  .chart-box.tall { height: 340px; }
  table.mini th { font-size: .7rem; text-transform: uppercase; letter-spacing: .04em; color:#5B5B5B; font-weight:600; text-align:left; padding:.4rem .5rem; border-bottom:1px solid #e5e7eb; }
  table.mini td { padding:.45rem .5rem; border-bottom:1px solid #f1f5f9; font-size:.8rem; vertical-align: top; }
  .kpi-num { font-variant-numeric: tabular-nums; }
</style>
</head>
<body class="font-sans text-ink">
<?php
$rp = function ($n) {
    $n = (float) $n;
    if ($n >= 1e12) return 'Rp ' . number_format($n / 1e12, 2, ',', '.') . ' T';
    if ($n >= 1e9)  return 'Rp ' . number_format($n / 1e9, 2, ',', '.') . ' M';
    if ($n >= 1e6)  return 'Rp ' . number_format($n / 1e6, 1, ',', '.') . ' jt';
    return 'Rp ' . number_format($n, 0, ',', '.');
};
$num = fn($n) => number_format((float) $n, 0, ',', '.');
$sub = $tahun ? 'Tahun ' . esc($tahun) : 'Semua tahun';
$pctTarget = $pct_target ?? 0;
$J = fn($x) => json_encode($x, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
?>
<div class="h-1.5 topbar-gradient"></div>

<nav class="sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-slate-200">
  <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between gap-3 flex-wrap">
    <div class="flex items-center gap-3 text-sm overflow-x-auto">
      <a href="<?= base_url('dashboard') ?>" class="text-ink-muted hover:text-brand whitespace-nowrap">&larr; Dashboard</a>
      <span class="text-slate-300">|</span>
      <a href="#ringkasan" class="hover:text-brand whitespace-nowrap">Ringkasan</a>
      <a href="#inovasi" class="hover:text-brand whitespace-nowrap">Inovasi</a>
      <a href="#kompetisi" class="hover:text-brand whitespace-nowrap">Kompetisi</a>
      <a href="#km" class="hover:text-brand whitespace-nowrap">Knowledge Mgmt</a>
      <a href="#peringkat" class="hover:text-brand whitespace-nowrap">Peringkat</a>
    </div>
    <form method="get" class="flex items-center gap-2 text-sm">
      <label for="tahun" class="text-ink-muted">Tahun:</label>
      <select id="tahun" name="tahun" onchange="this.form.submit()" class="border border-slate-300 rounded-lg px-2 py-1 text-sm">
        <option value="">Semua Tahun</option>
        <?php foreach ($daftar_tahun as $y): ?><option value="<?= esc($y) ?>" <?= (string) $tahun === (string) $y ? 'selected' : '' ?>><?= esc($y) ?></option><?php endforeach; ?>
      </select>
    </form>
  </div>
</nav>

<div class="max-w-7xl mx-auto px-4 pb-14 pt-5">

  <header class="flex items-center justify-between gap-4 bg-white rounded-2xl card border-t-4 border-brand px-6 py-5 mb-6">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold uppercase tracking-tight">Dashboard Eksekutif</h1>
      <p class="text-sm text-ink-muted mt-1">Inovasi &middot; Kompetisi &middot; Knowledge Management &middot; <?= $sub ?> &middot; per <?= esc($periode) ?></p>
    </div>
    <img src="<?= base_url('assets/img/logo-semen-padang.png') ?>" alt="Semen Padang" class="max-h-14 w-auto hidden sm:block">
  </header>

  <!-- ================= RINGKASAN (KPI) ================= -->
  <section id="ringkasan" class="mb-8">
    <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
      <?php
      $kpi = [
        ['Total Inovasi', $num($tot['n'] ?? 0), $sub, 'border-brand'],
        ['Total Benefit', $rp($tot['benefit'] ?? 0), ($tot['n_benefit'] ?? 0) . ' inovasi mencatat benefit', 'border-gold'],
        ['Total Saving', $rp($tot['saving'] ?? 0), 'Biaya proyek ' . $rp($tot['biaya'] ?? 0), 'border-gold'],
        ['Personil Inovasi', $num($personil_inovasi), 'Karyawan unik dalam tim', 'border-ink'],
        ['Penghargaan Kompetisi', $num($k_tot['n'] ?? 0), ($k_tot['events'] ?? 0) . ' event &middot; ' . $num($k_personil) . ' personil', 'border-brand'],
        ['Poin KM', $num($total_poin_km), $num($jumlah_aktivitas) . ' kegiatan KM', 'border-ink'],
      ];
      foreach ($kpi as [$label, $val, $hint, $bc]): ?>
        <div class="card border-l-4 <?= $bc ?> p-4">
          <p class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted"><?= $label ?></p>
          <p class="text-2xl font-bold mt-1 kpi-num"><?= $val ?></p>
          <p class="text-xs text-ink-muted mt-1"><?= $hint ?></p>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
      <div class="card p-5">
        <div class="flex justify-between items-baseline">
          <p class="text-sm font-semibold">Capaian Target Poin KM <?= esc($tahun_target) ?></p>
          <p class="text-sm font-bold text-brand"><?= $pct_target !== null ? esc($pct_target) . '%' : 'Target belum diatur' ?></p>
        </div>
        <div class="w-full h-3 bg-slate-100 rounded-full mt-3 overflow-hidden">
          <div class="h-3 rounded-full" style="width: <?= min(100, (float) $pctTarget) ?>%; background:#D73338"></div>
        </div>
        <p class="text-xs text-ink-muted mt-2"><?= $num($poin_tahun_target) ?> dari <?= $num($target_tahunan) ?> poin</p>
      </div>
      <div class="card p-5">
        <div class="flex justify-between items-baseline">
          <p class="text-sm font-semibold">Partisipasi Karyawan dalam KM</p>
          <p class="text-sm font-bold text-brand"><?= esc($pct_partisipasi) ?>%</p>
        </div>
        <div class="w-full h-3 bg-slate-100 rounded-full mt-3 overflow-hidden">
          <div class="h-3 rounded-full" style="width: <?= min(100, (float) $pct_partisipasi) ?>%; background:#272727"></div>
        </div>
        <p class="text-xs text-ink-muted mt-2"><?= $num($peserta_aktif) ?> dari <?= $num($total_karyawan) ?> karyawan pernah berpartisipasi</p>
      </div>
    </div>
  </section>

  <!-- ================= INOVASI ================= -->
  <section id="inovasi" class="mb-8">
    <h2 class="text-lg font-bold mb-3 flex items-center gap-2"><span class="w-1.5 h-5 bg-brand rounded"></span> Inovasi <span class="text-sm font-normal text-ink-muted">(<?= $sub ?>)</span></h2>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <div class="card p-5 lg:col-span-2">
        <p class="text-sm font-semibold mb-1">Tren Jumlah Inovasi per Tahun</p>
        <p class="text-xs text-ink-muted mb-3">Batang = jumlah inovasi, garis = total benefit (Rp miliar). Seluruh tahun, tidak terpengaruh filter.</p>
        <div class="chart-box"><canvas id="cTren"></canvas></div>
      </div>
      <div class="card p-5">
        <p class="text-sm font-semibold mb-3">Komposisi per Kategori</p>
        <div class="chart-box"><canvas id="cKategori"></canvas></div>
      </div>
      <div class="card p-5 lg:col-span-2">
        <p class="text-sm font-semibold mb-3">10 Unit/Departemen dengan Inovasi Terbanyak</p>
        <div class="chart-box tall"><canvas id="cDept"></canvas></div>
      </div>
      <div class="card p-5">
        <p class="text-sm font-semibold mb-3">Peran dalam Tim Inovasi</p>
        <div class="chart-box"><canvas id="cStruktur"></canvas></div>
      </div>
      <div class="card p-5 lg:col-span-2">
        <p class="text-sm font-semibold mb-2">10 Inovasi dengan Benefit Tertinggi</p>
        <div class="overflow-x-auto"><table class="mini w-full">
          <thead><tr><th>#</th><th>Judul</th><th>Tim</th><th>Kat.</th><th>Tahun</th><th class="text-right">Benefit</th></tr></thead>
          <tbody>
          <?php if (!$top_benefit): ?><tr><td colspan="6" class="text-center text-ink-muted py-4">Belum ada data benefit.</td></tr><?php endif; ?>
          <?php foreach ($top_benefit as $i => $r): ?>
            <tr><td><?= $i + 1 ?></td>
              <td><a class="hover:text-brand" href="<?= base_url('inovasi/' . $r['id']) ?>"><?= esc(mb_strimwidth((string) $r['judul_inovasi'], 0, 80, '…')) ?></a></td>
              <td><?= esc($r['nama_tim']) ?></td><td><?= esc($r['kategori_inovasi'] ?: '-') ?></td><td><?= esc($r['tahun']) ?></td>
              <td class="text-right font-semibold"><?= $rp($r['total_benefit']) ?></td></tr>
          <?php endforeach; ?>
          </tbody></table></div>
      </div>
      <div class="card p-5">
        <p class="text-sm font-semibold mb-2">Area Improvement Teratas</p>
        <div class="chart-box"><canvas id="cArea"></canvas></div>
      </div>
    </div>
  </section>

  <!-- ================= KOMPETISI ================= -->
  <section id="kompetisi" class="mb-8">
    <h2 class="text-lg font-bold mb-3 flex items-center gap-2"><span class="w-1.5 h-5 bg-gold rounded"></span> Kompetisi &amp; Penghargaan <span class="text-sm font-normal text-ink-muted">(<?= $sub ?>)</span></h2>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <div class="card p-5">
        <p class="text-sm font-semibold mb-3">Penghargaan per Jenis</p>
        <div class="chart-box"><canvas id="cJenis"></canvas></div>
      </div>
      <div class="card p-5">
        <p class="text-sm font-semibold mb-3">Penghargaan per Event</p>
        <div class="chart-box"><canvas id="cEvent"></canvas></div>
      </div>
      <div class="card p-5">
        <p class="text-sm font-semibold mb-3">Per Penyelenggara</p>
        <div class="chart-box"><canvas id="cPenyelenggara"></canvas></div>
      </div>
      <div class="card p-5 lg:col-span-1">
        <p class="text-sm font-semibold mb-3">Penghargaan per Tahun (berdasarkan jenis)</p>
        <div class="chart-box tall"><canvas id="cKomTahun"></canvas></div>
      </div>
      <div class="card p-5 lg:col-span-2">
        <p class="text-sm font-semibold mb-2">Penghargaan Terbaru</p>
        <div class="overflow-x-auto"><table class="mini w-full">
          <thead><tr><th>Judul Inovasi</th><th>Tim</th><th>Penghargaan</th><th>Event</th><th>Tanggal</th></tr></thead>
          <tbody>
          <?php if (!$k_terbaru): ?><tr><td colspan="5" class="text-center text-ink-muted py-4">Belum ada data kompetisi.</td></tr><?php endif; ?>
          <?php foreach ($k_terbaru as $r): ?>
            <tr><td><a class="hover:text-brand" href="<?= base_url('library/kompetisi/' . $r['id']) ?>"><?= esc(mb_strimwidth((string) $r['judul_inovasi'], 0, 70, '…')) ?></a></td>
              <td><?= esc($r['nama_tim']) ?></td>
              <td><span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800"><?= esc($r['jenis_penghargaan']) ?></span></td>
              <td><?= esc($r['event_kompetisi']) ?></td><td class="whitespace-nowrap"><?= esc($r['tanggal_penghargaan']) ?></td></tr>
          <?php endforeach; ?>
          </tbody></table></div>
      </div>
    </div>
  </section>

  <!-- ================= KM ================= -->
  <section id="km" class="mb-8">
    <h2 class="text-lg font-bold mb-3 flex items-center gap-2"><span class="w-1.5 h-5 bg-ink rounded"></span> Knowledge Management <span class="text-sm font-normal text-ink-muted">(akumulasi seluruh data)</span></h2>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <div class="card p-5 lg:col-span-2">
        <p class="text-sm font-semibold mb-1">Tren Poin &amp; Kegiatan per Bulan</p>
        <p class="text-xs text-ink-muted mb-3">Garis = poin, batang = jumlah kegiatan.</p>
        <div class="chart-box tall"><canvas id="cKmBulan"></canvas></div>
      </div>
      <div class="card p-5">
        <p class="text-sm font-semibold mb-3">Poin per Pilar KM</p>
        <div class="chart-box tall"><canvas id="cPilar"></canvas></div>
      </div>
      <div class="card p-5 lg:col-span-2">
        <p class="text-sm font-semibold mb-1">Tingkat Partisipasi per Band</p>
        <p class="text-xs text-ink-muted mb-3">Karyawan di tiap band yang tercatat di rekap KM dibanding total di Master Karyawan. Nilai di atas 100% berarti rekap memuat karyawan yang belum ada di Master.</p>
        <?php if (!$band_data): ?><p class="text-sm text-ink-muted py-6 text-center">Data Band belum tersedia &mdash; jalankan "Sinkron Band" di Master Karyawan KM.</p>
        <?php else: ?><div class="chart-box"><canvas id="cBand"></canvas></div><?php endif; ?>
      </div>
      <div class="card p-5">
        <p class="text-sm font-semibold mb-2">Aktivitas Sistem Terbaru</p>
        <ul class="text-sm divide-y divide-slate-100">
          <?php if (!$log_terbaru): ?><li class="py-3 text-ink-muted">Belum ada aktivitas.</li><?php endif; ?>
          <?php foreach ($log_terbaru as $l): ?>
            <li class="py-2"><span class="font-medium"><?= esc($l['nama_user'] ?? 'Sistem') ?></span>
              <span class="text-ink-muted"> &middot; <?= esc(str_replace('_', ' ', $l['action'] ?? '')) ?></span>
              <?php if (!empty($l['keterangan'])): ?><div class="text-xs text-ink-muted truncate"><?= esc($l['keterangan']) ?></div><?php endif; ?>
              <div class="text-[11px] text-ink-light"><?= esc($l['created_at']) ?></div></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </section>

  <!-- ================= PERINGKAT ================= -->
  <section id="peringkat" class="mb-4">
    <h2 class="text-lg font-bold mb-3 flex items-center gap-2"><span class="w-1.5 h-5 bg-brand rounded"></span> Peringkat KM</h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div class="card p-5">
        <p class="text-sm font-semibold mb-2">10 Karyawan Teratas</p>
        <table class="mini w-full"><thead><tr><th>#</th><th>Nama</th><th>Departemen</th><th>Band</th><th class="text-right">Poin</th></tr></thead><tbody>
          <?php foreach ($top_karyawan as $i => $r): ?>
            <tr><td><?= $i + 1 ?></td><td class="font-medium"><?= esc($r['nama']) ?></td><td><?= esc($r['departemen']) ?></td><td><?= esc($r['band'] ?: '-') ?></td><td class="text-right font-semibold"><?= $num($r['total_poin']) ?></td></tr>
          <?php endforeach; ?></tbody></table>
      </div>
      <div class="card p-5">
        <p class="text-sm font-semibold mb-2">Departemen Teratas (total poin)</p>
        <div class="chart-box tall"><canvas id="cTopDept"></canvas></div>
      </div>
    </div>
  </section>
</div>

<script>
const C = { red:'#D73338', redD:'#A8262A', redL:'#E8696D', ink:'#272727', gold:'#D97706', slate:'#64748B', teal:'#0F766E', sand:'#E5C07B', gray:'#CBD5E1' };
const PAL = [C.red, C.ink, C.gold, C.slate, C.teal, C.redL, C.sand, C.redD, C.gray];
Chart.defaults.animation = false;
Chart.defaults.font.family = 'Inter, sans-serif'; Chart.defaults.font.size = 11; Chart.defaults.color = '#5B5B5B';
const fmt = n => new Intl.NumberFormat('id-ID').format(n);
const mk = (id, cfg) => { const el = document.getElementById(id); if (el) { cfg.options = Object.assign({ responsive:true, maintainAspectRatio:false }, cfg.options || {}); new Chart(el, cfg); } };
const legendR = { plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8 } } } };
// gabungkan irisan kecil agar donut tetap terbaca: maksimal n irisan + "Lainnya"
const topN = (rows, key, n = 7) => {
  const a = rows.map(r => ({ l: r[key], v: +r.total })); if (a.length <= n) return a;
  const rest = a.slice(n).reduce((s, x) => s + x.v, 0); return a.slice(0, n).concat([{ l: 'Lainnya', v: rest }]);
};

const perTahun = <?= $J($per_tahun) ?>;
mk('cTren', { data: { labels: perTahun.map(r => r.tahun), datasets: [
  { type:'bar', label:'Jumlah inovasi', data: perTahun.map(r => +r.total), backgroundColor: C.red, borderRadius:4, yAxisID:'y' },
  { type:'line', label:'Total benefit (Rp miliar)', data: perTahun.map(r => Math.round(r.benefit/1e9)), borderColor: C.ink, backgroundColor: C.ink, tension:0, yAxisID:'y1' } ] },
  options: { scales: { y:{ beginAtZero:true, grid:{color:'#f1f5f9'} }, y1:{ position:'right', beginAtZero:true, grid:{display:false} } } } });

const kat = <?= $J($per_kategori) ?>;
const katN = topN(kat, 'kategori');
mk('cKategori', { type:'doughnut', data: { labels: katN.map(r => r.l), datasets:[{ data: katN.map(r => r.v), backgroundColor: PAL, borderWidth:0 }] }, options: Object.assign({ cutout:'58%' }, legendR) });

const dept = <?= $J($per_dept) ?>;
mk('cDept', { type:'bar', data: { labels: dept.map(r => r.dept), datasets:[{ data: dept.map(r => +r.total), backgroundColor: C.red, borderRadius:4 }] },
  options: { indexAxis:'y', plugins:{ legend:{display:false} }, scales:{ x:{ grid:{color:'#f1f5f9'} }, y:{ grid:{display:false} } } } });

const st = <?= $J($struktur_tim) ?>;
mk('cStruktur', { type:'doughnut', data: { labels: st.map(r => r.peran), datasets:[{ data: st.map(r => +r.total), backgroundColor: PAL, borderWidth:0 }] }, options: Object.assign({ cutout:'58%' }, legendR) });

const area = <?= $J($per_area) ?>;
mk('cArea', { type:'bar', data: { labels: area.map(r => r.area), datasets:[{ data: area.map(r => +r.total), backgroundColor: C.ink, borderRadius:4 }] },
  options: { indexAxis:'y', plugins:{ legend:{display:false} }, scales:{ x:{ grid:{color:'#f1f5f9'} }, y:{ grid:{display:false} } } } });

const jenis = <?= $J($k_jenis) ?>;
const jenisColor = { DIAMOND:'#0F766E', PLATINUM:'#64748B', GOLD:'#D97706', SILVER:'#9CA3AF', EXCELLENT:'#D73338' };
mk('cJenis', { type:'doughnut', data: { labels: jenis.map(r => r.jenis), datasets:[{ data: jenis.map(r => +r.total), backgroundColor: jenis.map((r,i) => jenisColor[r.jenis] || PAL[i % PAL.length]), borderWidth:0 }] }, options: Object.assign({ cutout:'58%' }, legendR) });

const ev = <?= $J($k_event) ?>;
mk('cEvent', { type:'bar', data: { labels: ev.map(r => r.event), datasets:[{ data: ev.map(r => +r.total), backgroundColor: C.gold, borderRadius:4 }] },
  options: { plugins:{ legend:{display:false} }, scales:{ y:{ beginAtZero:true, grid:{color:'#f1f5f9'} }, x:{ grid:{display:false} } } } });

const pen = <?= $J($k_penyelenggara) ?>;
mk('cPenyelenggara', { type:'doughnut', data: { labels: pen.map(r => r.nama), datasets:[{ data: pen.map(r => +r.total), backgroundColor: PAL, borderWidth:0 }] }, options: Object.assign({ cutout:'58%' }, legendR) });

const ktj = <?= $J($k_tahun_jenis) ?>;
const yrs = [...new Set(ktj.map(r => r.tahun))].sort(); const jn = [...new Set(ktj.map(r => r.jenis))];
mk('cKomTahun', { type:'bar', data: { labels: yrs, datasets: jn.map((j,i) => ({ label:j, backgroundColor: jenisColor[j] || PAL[i % PAL.length],
    data: yrs.map(y => { const f = ktj.find(r => r.tahun === y && r.jenis === j); return f ? +f.total : 0; }) })) },
  options: { scales:{ x:{ stacked:true, grid:{display:false} }, y:{ stacked:true, beginAtZero:true, grid:{color:'#f1f5f9'} } }, plugins:{ legend:{ position:'bottom', labels:{ boxWidth:10 } } } } });

const tren = <?= $J($tren_bulanan) ?>; const keg = <?= $J($kegiatan_bulanan) ?>;
const kegMap = {}; keg.forEach(r => kegMap[String(r.bulan).toUpperCase()] = +r.kegiatan);
const ttl = s => s.charAt(0) + s.slice(1).toLowerCase();
mk('cKmBulan', { data: { labels: tren.map(r => ttl(r.bulan)), datasets: [
  { type:'bar', label:'Kegiatan', data: tren.map(r => kegMap[r.bulan] || 0), backgroundColor:'#E5E7EB', borderRadius:4, yAxisID:'y1' },
  { type:'line', label:'Poin', data: tren.map(r => +r.total_poin), borderColor: C.red, backgroundColor: C.red, tension:0, yAxisID:'y' } ] },
  options: { scales: { y:{ beginAtZero:true, grid:{color:'#f1f5f9'}, ticks:{ callback: v => fmt(v) } }, y1:{ position:'right', beginAtZero:true, grid:{display:false} } } } });

const pil = <?= $J($per_pilar) ?>;
mk('cPilar', { type:'doughnut', data: { labels: pil.map(r => r.pilar), datasets:[{ data: pil.map(r => +r.poin), backgroundColor: PAL, borderWidth:0 }] },
  options: Object.assign({ cutout:'55%', plugins:{ legend:{ position:'bottom', labels:{ boxWidth:10 } }, tooltip:{ callbacks:{ label: c => c.label + ': ' + fmt(c.parsed) + ' poin' } } } }) });

const band = <?= $J($band_data) ?>;
mk('cBand', { data: { labels: band.map(r => 'Band ' + r.band), datasets: [
  { type:'bar', label:'Total karyawan', data: band.map(r => r.total), backgroundColor:'#E5E7EB', borderRadius:4, yAxisID:'y' },
  { type:'bar', label:'Terlibat KM', data: band.map(r => r.terlibat), backgroundColor: C.red, borderRadius:4, yAxisID:'y' },
  { type:'line', label:'Partisipasi (%)', data: band.map(r => r.pct), borderColor: C.ink, backgroundColor: C.ink, tension:0, yAxisID:'y1' } ] },
  options: { scales: { y:{ beginAtZero:true, grid:{color:'#f1f5f9'} }, y1:{ position:'right', beginAtZero:true, grid:{display:false}, ticks:{ callback: v => v + '%' } } } } });

const td = <?= $J($top_departemen) ?>;
mk('cTopDept', { type:'bar', data: { labels: td.map(r => r.departemen), datasets:[{ data: td.map(r => +r.total_poin), backgroundColor: C.red, borderRadius:4 }] },
  options: { indexAxis:'y', plugins:{ legend:{display:false} }, scales:{ x:{ grid:{color:'#f1f5f9'}, ticks:{ callback: v => fmt(v) } }, y:{ grid:{display:false} } } } });
</script>
</body>
</html>
