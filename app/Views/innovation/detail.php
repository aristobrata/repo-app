<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php if (!empty($inovasi['cover_thumbnail'])): ?>
    <img src="<?= base_url('inovasi-image/' . $inovasi['cover_thumbnail']) ?>" class="img-fluid rounded mb-3" style="max-height:280px; object-fit:cover; width:100%;">
<?php endif; ?>

<span class="badge bg-secondary mb-2"><?= esc($inovasi['kategori_inovasi']) ?></span>
<h4><?= esc($inovasi['judul_inovasi']) ?></h4>
<p class="text-muted">Tim: <?= esc($inovasi['nama_tim']) ?> &middot; Tahun: <?= esc($inovasi['tahun']) ?> &middot; Status: <?= esc($inovasi['status_saat_ini']) ?></p>

<div class="mb-3">
    <button id="btn-like" class="btn btn-sm <?= $sudah_like ? 'btn-danger' : 'btn-outline-danger' ?>" data-id="<?= $inovasi['id'] ?>">
        👍 <span id="like-count"><?= (int) $inovasi['jumlah_like'] ?></span> Apresiasi
    </button>
    <?php if (!empty($inovasi['file_dokumen'])): ?>
        <a href="<?= base_url('inovasi/' . $inovasi['id'] . '/download') ?>" class="btn btn-sm btn-outline-secondary">⬇️ Download Dokumen</a>
    <?php elseif (!empty($inovasi['hyperlink_dokumen'])): ?>
        <span class="badge bg-light text-dark border">Referensi dokumen lama: <?= esc($inovasi['hyperlink_dokumen']) ?></span>
    <?php endif; ?>
</div>

<div class="row mb-3">
    <div class="col-md-8">
        <h6>Area Improvement</h6>
        <p><?= esc($inovasi['area_improvement'] ?: '-') ?></p>
        <h6>Area Implementasi</h6>
        <p><?= esc($inovasi['unit_dept_area_implementasi']) ?> / <?= esc($inovasi['unit_biro_area_implementasi']) ?></p>
        <?php if (!empty($inovasi['keterangan'])): ?>
            <h6>Keterangan</h6>
            <p><?= nl2br(esc($inovasi['keterangan'])) ?></p>
        <?php endif; ?>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body small">
                <p class="mb-1 d-flex justify-content-between"><span>Biaya Project</span><strong>Rp <?= number_format($inovasi['biaya_project'] ?? 0, 0, ',', '.') ?></strong></p>
                <p class="mb-1 d-flex justify-content-between"><span>Saving</span><strong>Rp <?= number_format($inovasi['saving'] ?? 0, 0, ',', '.') ?></strong></p>
                <p class="mb-1 d-flex justify-content-between"><span>Opportunity Lost</span><strong>Rp <?= number_format($inovasi['opp_lost'] ?? 0, 0, ',', '.') ?></strong></p>
                <p class="mb-1 d-flex justify-content-between"><span>Revenue</span><strong>Rp <?= number_format($inovasi['revenue'] ?? 0, 0, ',', '.') ?></strong></p>
                <hr class="my-1">
                <p class="mb-0 d-flex justify-content-between"><span>Total Benefit</span><strong class="text-success">Rp <?= number_format($inovasi['total_benefit'] ?? 0, 0, ',', '.') ?></strong></p>
            </div>
        </div>
    </div>
</div>

<h6>Anggota Tim</h6>
<table class="table table-sm">
    <thead><tr><th>Nama</th><th>NIK</th><th>Peran</th><th>Unit</th></tr></thead>
    <tbody>
    <?php foreach ($tim as $t): ?>
        <tr><td><?= esc($t['nama_personil']) ?></td><td><?= esc($t['nik']) ?></td><td><?= esc($t['struktur_tim']) ?></td><td><?= esc($t['org_unit']) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php if (!empty($lampiran)): ?>
    <h6>Lampiran Tambahan</h6>
    <ul><?php foreach ($lampiran as $l): ?><li><?= esc($l['nama_file']) ?></li><?php endforeach; ?></ul>
<?php endif; ?>

<?= $this->section('scripts') ?>
<script>
const csrfName = '<?= csrf_token() ?>';
const csrfHash = '<?= csrf_hash() ?>';
document.getElementById('btn-like').addEventListener('click', async function () {
    const id = this.dataset.id;
    const res = await fetch(`<?= base_url('inovasi') ?>/${id}/like`, { method: 'POST', headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'}, body: `${csrfName}=${csrfHash}` });
    const data = await res.json();
    document.getElementById('like-count').innerText = data.total_like;
    this.classList.toggle('btn-danger', data.liked);
    this.classList.toggle('btn-outline-danger', !data.liked);
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
