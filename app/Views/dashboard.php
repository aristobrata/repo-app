<?= $this->extend('layouts/main') ?>
<?= $this->section('styles') ?>
<script src="https://cdn.tailwindcss.com"></script>
<script>
  // Tailwind memakai prefix 'tw-', tanpa preflight, dan !important, supaya tidak bentrok dengan Bootstrap milik layout utama.
  tailwind.config = { prefix: 'tw-', important: true, corePlugins: { preflight: false }, theme: { extend: {
    colors: { brand: { DEFAULT: '#D73338', dark: '#A8262A', light: '#E8696D' },
              ink: { DEFAULT: '#272727', muted: '#5B5B5B', light: '#9CA3AF' }, gold: { DEFAULT: '#D97706' } } } } };
</script>
<style>
  html { scroll-behavior: smooth; scroll-padding-top: 130px; }
  #exec { font-family: Inter, sans-serif; color:#272727; }
  #dash-subnav { top: 60px; }
  #exec a { text-decoration:none; color:inherit; }
  #exec ul { list-style:none; padding:0; margin:0; }
  #exec h1,#exec h2 { margin:0; }
  #exec p { margin-bottom:0; }
  #exec table.mini { margin:0; }
  .dcard { background:#fff; border-radius:1rem; box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 4px 10px rgba(15,23,42,.04); }
    .chart-box { position: relative; height: 280px; }
  .chart-box.tall { height: 340px; }
  table.mini th { font-size: .7rem; text-transform: uppercase; letter-spacing: .04em; color:#5B5B5B; font-weight:600; text-align:left; padding:.4rem .5rem; border-bottom:1px solid #e5e7eb; }
  table.mini td { padding:.45rem .5rem; border-bottom:1px solid #f1f5f9; font-size:.8rem; vertical-align: top; }
  .kpi-num { font-variant-numeric: tabular-nums; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
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
<div id="exec">

<nav id="dash-subnav" class="tw-sticky tw-z-30 tw-bg-white tw-border-b tw-border-slate-200 tw-rounded-xl tw-mb-4 tw-shadow-sm">
  <div class="tw-px-4 tw-py-2 tw-flex tw-items-center tw-justify-between tw-gap-3 tw-flex-wrap">
    <div class="tw-flex tw-items-center tw-gap-3 tw-text-sm tw-overflow-x-auto">
      <a href="#ringkasan" class="hover:tw-text-brand tw-whitespace-nowrap">Ringkasan</a>
      <a href="#inovasi" class="hover:tw-text-brand tw-whitespace-nowrap">Inovasi</a>
      <a href="#kompetisi" class="hover:tw-text-brand tw-whitespace-nowrap">Kompetisi</a>
      <a href="#km" class="hover:tw-text-brand tw-whitespace-nowrap">Knowledge Mgmt</a>
      <a href="#peringkat" class="hover:tw-text-brand tw-whitespace-nowrap">Peringkat</a>
    </div>
    <form method="get" class="tw-flex tw-items-center tw-gap-2 tw-text-sm">
      <label for="tahun" class="tw-text-ink-muted">Tahun:</label>
      <select id="tahun" name="tahun" onchange="this.form.submit()" class="tw-border tw-border-slate-300 tw-rounded-lg tw-px-2 tw-py-1 tw-text-sm">
        <option value="">Semua Tahun</option>
        <?php foreach ($daftar_tahun as $y): ?><option value="<?= esc($y) ?>" <?= (string) $tahun === (string) $y ? 'selected' : '' ?>><?= esc($y) ?></option><?php endforeach; ?>
      </select>
    </form>
  </div>
</nav>

<div class="tw-pb-6">

  <header class="tw-flex tw-items-center tw-justify-between tw-gap-4 tw-bg-white tw-rounded-2xl dcard tw-border-t-4 tw-border-brand tw-px-6 tw-py-5 tw-mb-6">
    <div>
      <h1 class="tw-text-2xl md:tw-text-3xl tw-font-extrabold tw-uppercase tw-tracking-tight">Dashboard Knowledge Management &amp; Inovasi</h1>
      <p class="tw-text-sm tw-text-ink-muted tw-mt-1">Inovasi &middot; Kompetisi &middot; Knowledge Management &middot; <?= $sub ?> &middot; per <?= esc($periode) ?></p>
    </div>
  </header>

  <!-- ================= RINGKASAN (KPI) ================= -->
  <section id="ringkasan" class="tw-mb-8">
    <div class="tw-grid tw-grid-cols-2 lg:tw-grid-cols-4 xl:tw-grid-cols-6 tw-gap-4">
      <?php
      $kpi = [
        ['Total Inovasi', $num($tot['n'] ?? 0), $sub, 'tw-border-brand'],
        ['Total Benefit', $rp($tot['benefit'] ?? 0), ($tot['n_benefit'] ?? 0) . ' inovasi mencatat benefit', 'tw-border-gold'],
        ['Total Saving', $rp($tot['saving'] ?? 0), 'Biaya proyek ' . $rp($tot['biaya'] ?? 0), 'tw-border-gold'],
        ['Personil Inovasi', $num($personil_inovasi), 'Karyawan unik dalam tim', 'tw-border-ink'],
        ['Penghargaan Kompetisi', $num($k_tot['n'] ?? 0), ($k_tot['events'] ?? 0) . ' event &middot; ' . $num($k_personil) . ' personil', 'tw-border-brand'],
        ['Poin KM', $num($total_poin_km), $num($jumlah_aktivitas) . ' kegiatan KM', 'tw-border-ink'],
      ];
      foreach ($kpi as [$label, $val, $hint, $bc]): ?>
        <div class="dcard tw-border-l-4 <?= $bc ?> tw-p-4">
          <p class="tw-text-[11px] tw-font-semibold tw-uppercase tw-tracking-wide tw-text-ink-muted"><?= $label ?></p>
          <p class="tw-text-2xl tw-font-bold tw-mt-1 kpi-num"><?= $val ?></p>
          <p class="tw-text-xs tw-text-ink-muted tw-mt-1"><?= $hint ?></p>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4 tw-mt-4">
      <div class="dcard tw-p-5">
        <div class="tw-flex tw-justify-between tw-items-baseline">
          <p class="tw-text-sm tw-font-semibold">Capaian Target Poin KM <?= esc($tahun_target) ?></p>
          <p class="tw-text-sm tw-font-bold tw-text-brand"><?= $pct_target !== null ? esc($pct_target) . '%' : 'Target belum diatur' ?></p>
        </div>
        <div class="tw-w-full tw-h-3 tw-bg-slate-100 tw-rounded-full tw-mt-3 tw-overflow-hidden">
          <div class="tw-h-3 tw-rounded-full" style="width: <?= min(100, (float) $pctTarget) ?>%; background:#D73338"></div>
        </div>
        <p class="tw-text-xs tw-text-ink-muted tw-mt-2"><?= $num($poin_tahun_target) ?> dari <?= $num($target_tahunan) ?> poin</p>
      </div>
      <div class="dcard tw-p-5">
        <div class="tw-flex tw-justify-between tw-items-baseline">
          <p class="tw-text-sm tw-font-semibold">Partisipasi Karyawan dalam KM</p>
          <p class="tw-text-sm tw-font-bold tw-text-brand"><?= esc($pct_partisipasi) ?>%</p>
        </div>
        <div class="tw-w-full tw-h-3 tw-bg-slate-100 tw-rounded-full tw-mt-3 tw-overflow-hidden">
          <div class="tw-h-3 tw-rounded-full" style="width: <?= min(100, (float) $pct_partisipasi) ?>%; background:#272727"></div>
        </div>
        <p class="tw-text-xs tw-text-ink-muted tw-mt-2"><?= $num($peserta_aktif) ?> dari <?= $num($total_karyawan) ?> karyawan pernah berpartisipasi</p>
      </div>
    </div>
  </section>

  <!-- ================= INOVASI ================= -->
  <section id="inovasi" class="tw-mb-8">
    <h2 class="tw-text-lg tw-font-bold tw-mb-3 tw-flex tw-items-center tw-gap-2"><span class="tw-w-1.5 tw-h-5 tw-bg-brand tw-rounded"></span> Inovasi <span class="tw-text-sm tw-font-normal tw-text-ink-muted">(<?= $sub ?>)</span></h2>
    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-4">
      <div class="dcard tw-p-5 lg:tw-col-span-2">
        <p class="tw-text-sm tw-font-semibold tw-mb-1">Tren Jumlah Inovasi per Tahun</p>
        <p class="tw-text-xs tw-text-ink-muted tw-mb-3">Batang = jumlah inovasi, garis = total benefit (Rp miliar). Seluruh tahun, tidak terpengaruh filter.</p>
        <div class="chart-box"><canvas id="cTren"></canvas></div>
      </div>
      <div class="dcard tw-p-5">
        <p class="tw-text-sm tw-font-semibold tw-mb-3">Komposisi per Kategori</p>
        <div class="chart-box"><canvas id="cKategori"></canvas></div>
      </div>
      <div class="dcard tw-p-5 lg:tw-col-span-2">
        <p class="tw-text-sm tw-font-semibold tw-mb-3">10 Unit/Departemen dengan Inovasi Terbanyak</p>
        <div class="chart-box tall"><canvas id="cDept"></canvas></div>
      </div>
      <div class="dcard tw-p-5">
        <p class="tw-text-sm tw-font-semibold tw-mb-3">Peran dalam Tim Inovasi</p>
        <div class="chart-box"><canvas id="cStruktur"></canvas></div>
      </div>
      <div class="dcard tw-p-5 lg:tw-col-span-2">
        <p class="tw-text-sm tw-font-semibold tw-mb-2">10 Inovasi dengan Benefit Tertinggi</p>
        <div class="tw-overflow-x-auto"><table class="mini tw-w-full">
          <thead><tr><th>#</th><th>Judul</th><th>Tim</th><th>Kat.</th><th>Tahun</th><th class="tw-text-right">Benefit</th></tr></thead>
          <tbody>
          <?php if (!$top_benefit): ?><tr><td colspan="6" class="tw-text-center tw-text-ink-muted tw-py-4">Belum ada data benefit.</td></tr><?php endif; ?>
          <?php foreach ($top_benefit as $i => $r): ?>
            <tr><td><?= $i + 1 ?></td>
              <td><a class="hover:tw-text-brand" href="<?= base_url('inovasi/' . $r['id']) ?>"><?= esc(mb_strimwidth((string) $r['judul_inovasi'], 0, 80, '…')) ?></a></td>
              <td><?= esc($r['nama_tim']) ?></td><td><?= esc($r['kategori_inovasi'] ?: '-') ?></td><td><?= esc($r['tahun']) ?></td>
              <td class="tw-text-right tw-font-semibold"><?= $rp($r['total_benefit']) ?></td></tr>
          <?php endforeach; ?>
          </tbody></table></div>
      </div>
      <div class="dcard tw-p-5">
        <p class="tw-text-sm tw-font-semibold tw-mb-2">Area Improvement Teratas</p>
        <div class="chart-box"><canvas id="cArea"></canvas></div>
      </div>
    </div>
  </section>

  <!-- ================= KOMPETISI ================= -->
  <section id="kompetisi" class="tw-mb-8">
    <h2 class="tw-text-lg tw-font-bold tw-mb-3 tw-flex tw-items-center tw-gap-2"><span class="tw-w-1.5 tw-h-5 tw-bg-gold tw-rounded"></span> Kompetisi &amp; Penghargaan <span class="tw-text-sm tw-font-normal tw-text-ink-muted">(<?= $sub ?>)</span></h2>
    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-4">
      <div class="dcard tw-p-5">
        <p class="tw-text-sm tw-font-semibold tw-mb-3">Penghargaan per Jenis</p>
        <div class="chart-box"><canvas id="cJenis"></canvas></div>
      </div>
      <div class="dcard tw-p-5">
        <p class="tw-text-sm tw-font-semibold tw-mb-3">Penghargaan per Event</p>
        <div class="chart-box"><canvas id="cEvent"></canvas></div>
      </div>
      <div class="dcard tw-p-5">
        <p class="tw-text-sm tw-font-semibold tw-mb-3">Per Penyelenggara</p>
        <div class="chart-box"><canvas id="cPenyelenggara"></canvas></div>
      </div>
      <div class="dcard tw-p-5 lg:tw-col-span-1">
        <p class="tw-text-sm tw-font-semibold tw-mb-3">Penghargaan per Tahun (berdasarkan jenis)</p>
        <div class="chart-box tall"><canvas id="cKomTahun"></canvas></div>
      </div>
      <div class="dcard tw-p-5 lg:tw-col-span-2">
        <p class="tw-text-sm tw-font-semibold tw-mb-2">Penghargaan Terbaru</p>
        <div class="tw-overflow-x-auto"><table class="mini tw-w-full">
          <thead><tr><th>Judul Inovasi</th><th>Tim</th><th>Penghargaan</th><th>Event</th><th>Tanggal</th></tr></thead>
          <tbody>
          <?php if (!$k_terbaru): ?><tr><td colspan="5" class="tw-text-center tw-text-ink-muted tw-py-4">Belum ada data kompetisi.</td></tr><?php endif; ?>
          <?php foreach ($k_terbaru as $r): ?>
            <tr><td><a class="hover:tw-text-brand" href="<?= base_url('library/kompetisi/' . $r['id']) ?>"><?= esc(mb_strimwidth((string) $r['judul_inovasi'], 0, 70, '…')) ?></a></td>
              <td><?= esc($r['nama_tim']) ?></td>
              <td><span class="tw-px-2 tw-py-0.5 tw-rounded-full tw-text-[11px] tw-font-semibold tw-bg-amber-100 tw-text-amber-800"><?= esc($r['jenis_penghargaan']) ?></span></td>
              <td><?= esc($r['event_kompetisi']) ?></td><td class="tw-whitespace-nowrap"><?= esc($r['tanggal_penghargaan']) ?></td></tr>
          <?php endforeach; ?>
          </tbody></table></div>
      </div>
    </div>
  </section>

  <!-- ================= KM ================= -->
  <section id="km" class="tw-mb-8">
    <h2 class="tw-text-lg tw-font-bold tw-mb-3 tw-flex tw-items-center tw-gap-2"><span class="tw-w-1.5 tw-h-5 tw-bg-ink tw-rounded"></span> Knowledge Management <span class="tw-text-sm tw-font-normal tw-text-ink-muted">(akumulasi seluruh data)</span></h2>
    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-4">
      <div class="dcard tw-p-5 lg:tw-col-span-2">
        <p class="tw-text-sm tw-font-semibold tw-mb-1">Tren Poin &amp; Kegiatan per Bulan</p>
        <p class="tw-text-xs tw-text-ink-muted tw-mb-3">Garis = poin, batang = jumlah kegiatan.</p>
        <div class="chart-box tall"><canvas id="cKmBulan"></canvas></div>
      </div>
      <div class="dcard tw-p-5">
        <p class="tw-text-sm tw-font-semibold tw-mb-3">Poin per Pilar KM</p>
        <div class="chart-box tall"><canvas id="cPilar"></canvas></div>
      </div>
      <div class="dcard tw-p-5 lg:tw-col-span-2">
        <p class="tw-text-sm tw-font-semibold tw-mb-1">Tingkat Partisipasi per Band</p>
        <p class="tw-text-xs tw-text-ink-muted tw-mb-3">Karyawan di tiap band yang tercatat di rekap KM dibanding total di Master Karyawan. Nilai di atas 100% berarti rekap memuat karyawan yang belum ada di Master.</p>
        <?php if (!$band_data): ?><p class="tw-text-sm tw-text-ink-muted tw-py-6 tw-text-center">Data Band belum tersedia &mdash; jalankan "Sinkron Band" di Master Karyawan KM.</p>
        <?php else: ?><div class="chart-box tall"><canvas id="cBand"></canvas></div><?php endif; ?>
      </div>
      <div class="dcard tw-p-5">
        <p class="tw-text-sm tw-font-semibold tw-mb-2">Aktivitas Sistem Terbaru</p>
        <ul class="tw-text-sm tw-divide-y tw-divide-slate-100">
          <?php if (!$log_terbaru): ?><li class="tw-py-3 tw-text-ink-muted">Belum ada aktivitas.</li><?php endif; ?>
          <?php foreach ($log_terbaru as $l): ?>
            <li class="tw-py-2"><span class="tw-font-medium"><?= esc($l['nama_user'] ?? 'Sistem') ?></span>
              <span class="tw-text-ink-muted"> &middot; <?= esc(str_replace('_', ' ', $l['action'] ?? '')) ?></span>
              <?php if (!empty($l['keterangan'])): ?><div class="tw-text-xs tw-text-ink-muted tw-truncate"><?= esc($l['keterangan']) ?></div><?php endif; ?>
              <div class="tw-text-[11px] tw-text-ink-light"><?= esc($l['created_at']) ?></div></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </section>

  <!-- ================= PERINGKAT ================= -->
  <section id="peringkat" class="tw-mb-4">
    <h2 class="tw-text-lg tw-font-bold tw-mb-3 tw-flex tw-items-center tw-gap-2"><span class="tw-w-1.5 tw-h-5 tw-bg-brand tw-rounded"></span> Peringkat KM</h2>
    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-2 tw-gap-4">
      <div class="dcard tw-p-5">
        <p class="tw-text-sm tw-font-semibold tw-mb-2">10 Karyawan Teratas</p>
        <table class="mini tw-w-full"><thead><tr><th>#</th><th>Nama</th><th>Departemen</th><th>Band</th><th class="tw-text-right">Poin</th></tr></thead><tbody>
          <?php foreach ($top_karyawan as $i => $r): ?>
            <tr><td><?= $i + 1 ?></td><td class="tw-font-medium"><?= esc($r['nama']) ?></td><td><?= esc($r['departemen']) ?></td><td><?= esc($r['band'] ?: '-') ?></td><td class="tw-text-right tw-font-semibold"><?= $num($r['total_poin']) ?></td></tr>
          <?php endforeach; ?></tbody></table>
      </div>
      <div class="dcard tw-p-5">
        <p class="tw-text-sm tw-font-semibold tw-mb-2">Departemen Teratas (total poin)</p>
        <div class="chart-box tall"><canvas id="cTopDept"></canvas></div>
      </div>
    </div>
  </section>
</div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const C = { red:'#D73338', redD:'#A8262A', redL:'#E8696D', ink:'#272727', gold:'#D97706', slate:'#64748B', teal:'#0F766E', sand:'#E5C07B', gray:'#CBD5E1' };
const PAL = [C.red, C.ink, C.gold, C.slate, C.teal, C.redL, C.sand, C.redD, C.gray];
// sub-menu dashboard menempel tepat di bawah navbar utama (yang sudah sticky)
(function () { var nb = document.querySelector('nav.navbar'), sn = document.getElementById('dash-subnav');
  if (nb && sn) { var f = function () { sn.style.top = nb.offsetHeight + 'px'; document.documentElement.style.scrollPaddingTop = (nb.offsetHeight + sn.offsetHeight + 12) + 'px'; }; f(); window.addEventListener('resize', f); } })();
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
<?= $this->endSection() ?>
