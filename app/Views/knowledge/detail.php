<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php if (!empty($item['foto_sampul'])): ?>
    <img src="<?= base_url('knowledge-image/' . $item['foto_sampul']) ?>" class="img-fluid rounded mb-3" style="max-height:320px; object-fit:cover; width:100%;">
<?php endif; ?>

<span class="badge bg-info mb-2"><?= esc($item['topik'] ?? 'Knowledge') ?></span>
<h4><?= esc($item['judul']) ?></h4>
<p class="text-muted">Oleh: <?= esc($item['nama_penulis']) ?> · Divisi: <?= esc($item['divisi']) ?></p>

<div class="mb-3">
    <button id="btn-like" class="btn btn-sm <?= $sudah_like ? 'btn-danger' : 'btn-outline-danger' ?>" data-id="<?= $item['id'] ?>">
        👍 <span id="like-count"><?= (int) $item['jumlah_like'] ?></span> Apresiasi
    </button>
    <button id="btn-bookmark" class="btn btn-sm <?= $sudah_bookmark ? 'btn-warning' : 'btn-outline-warning' ?>" data-id="<?= $item['id'] ?>">
        🔖 <?= $sudah_bookmark ? 'Tersimpan' : 'Simpan' ?>
    </button>
</div>

<h6>Ringkasan</h6>
<p><?= nl2br(esc($item['ringkasan'])) ?></p>
<h6>Konten Lengkap</h6>
<p><?= nl2br(esc($item['konten'])) ?></p>
<?php if (!empty($item['referensi'])): ?>
    <h6>Referensi</h6>
    <p><?= nl2br(esc($item['referensi'])) ?></p>
<?php endif; ?>

<?php if (!empty($lampiran)): ?>
    <h6>Lampiran Dokumen Pendukung</h6>
    <ul><?php foreach ($lampiran as $l): ?><li><?= esc($l['nama_file']) ?></li><?php endforeach; ?></ul>
<?php endif; ?>

<?= $this->section('scripts') ?>
<script>
const csrfName = '<?= csrf_token() ?>';
const csrfHash = '<?= csrf_hash() ?>';
document.getElementById('btn-like').addEventListener('click', async function () {
    const id = this.dataset.id;
    const res = await fetch(`<?= base_url('pengetahuan') ?>/${id}/like`, { method: 'POST', headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'}, body: `${csrfName}=${csrfHash}` });
    const data = await res.json();
    document.getElementById('like-count').innerText = data.total_like;
    this.classList.toggle('btn-danger', data.liked);
    this.classList.toggle('btn-outline-danger', !data.liked);
});
document.getElementById('btn-bookmark').addEventListener('click', async function () {
    const id = this.dataset.id;
    const res = await fetch(`<?= base_url('pengetahuan') ?>/${id}/bookmark`, { method: 'POST', headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'}, body: `${csrfName}=${csrfHash}` });
    const data = await res.json();
    this.innerText = data.bookmarked ? '🔖 Tersimpan' : '🔖 Simpan';
    this.classList.toggle('btn-warning', data.bookmarked);
    this.classList.toggle('btn-outline-warning', !data.bookmarked);
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
