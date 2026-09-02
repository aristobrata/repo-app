<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php if (!empty($inovasi['foto_ilustrasi'])): ?>
    <img src="<?= base_url('inovasi-image/' . $inovasi['foto_ilustrasi']) ?>" class="img-fluid rounded mb-3" style="max-height:320px; object-fit:cover; width:100%;">
<?php endif; ?>

<h4><?= esc($inovasi['judul']) ?></h4>
<p class="text-muted">Oleh: <?= esc($inovasi['nama_karyawan']) ?> · Divisi: <?= esc($inovasi['divisi']) ?></p>

<div class="mb-3">
    <button id="btn-like" class="btn btn-sm <?= $sudah_like ? 'btn-danger' : 'btn-outline-danger' ?>" data-id="<?= $inovasi['id'] ?>">
        👍 <span id="like-count"><?= (int) $inovasi['jumlah_like'] ?></span> Apresiasi
    </button>
    <button id="btn-bookmark" class="btn btn-sm <?= $sudah_bookmark ? 'btn-warning' : 'btn-outline-warning' ?>" data-id="<?= $inovasi['id'] ?>">
        🔖 <?= $sudah_bookmark ? 'Tersimpan' : 'Simpan' ?>
    </button>
</div>

<h6>Deskripsi Masalah</h6>
<p><?= nl2br(esc($inovasi['deskripsi_masalah'])) ?></p>

<h6>Solusi Inovatif</h6>
<p><?= nl2br(esc($inovasi['solusi_inovatif'])) ?></p>

<h6>Dampak/Manfaat bagi Perusahaan</h6>
<p><?= nl2br(esc($inovasi['dampak_manfaat'])) ?></p>

<?php if (!empty($lampiran)): ?>
    <h6>Lampiran Dokumen Pendukung</h6>
    <ul>
        <?php foreach ($lampiran as $l): ?>
            <li><?= esc($l['nama_file']) ?> (<?= esc($l['tipe_file']) ?>)</li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?= $this->section('scripts') ?>
<script>
const csrfName = '<?= csrf_token() ?>';
const csrfHash = '<?= csrf_hash() ?>';

document.getElementById('btn-like').addEventListener('click', async function () {
    const id = this.dataset.id;
    const res = await fetch(`<?= base_url('inovasi') ?>/${id}/like`, {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
        body: `${csrfName}=${csrfHash}`
    });
    const data = await res.json();
    document.getElementById('like-count').innerText = data.total_like;
    this.classList.toggle('btn-danger', data.liked);
    this.classList.toggle('btn-outline-danger', !data.liked);
});

document.getElementById('btn-bookmark').addEventListener('click', async function () {
    const id = this.dataset.id;
    const res = await fetch(`<?= base_url('inovasi') ?>/${id}/bookmark`, {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
        body: `${csrfName}=${csrfHash}`
    });
    const data = await res.json();
    this.innerText = data.bookmarked ? '🔖 Tersimpan' : '🔖 Simpan';
    this.classList.toggle('btn-warning', data.bookmarked);
    this.classList.toggle('btn-outline-warning', !data.bookmarked);
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
