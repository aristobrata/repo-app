<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<img src="<?= !empty($item['cover']) ? base_url('library-image/' . $item['cover']) : base_url('assets/img/no-cover.svg') ?>"
     class="rounded mb-3 border" style="display:block; width:auto; height:auto; max-width:100%; background:#E2E8F0;">

<span class="badge bg-info text-dark mb-2"><?= esc($item['pillar_km'] ?? 'Knowledge Management') ?></span>
<h4><?= esc($item['judul_event']) ?></h4>
<p class="text-muted">
    <?= esc($item['bulan']) ?> <?= esc($item['tahun']) ?>
    <?php if (!empty($item['tempat'])): ?> · <?= esc($item['tempat']) ?><?php endif; ?>
</p>

<table class="table table-sm w-auto">
    <tr><th class="pe-4">Aktivitas</th><td><?= esc($item['aktivitas'] ?? '-') ?></td></tr>
    <tr><th class="pe-4">Subactivity</th><td><?= esc($item['subactivity'] ?? '-') ?></td></tr>
    <tr><th class="pe-4">Nama Peserta</th><td><?= esc($item['nama_peserta'] ?? '-') ?></td></tr>
    <tr><th class="pe-4">Peran</th><td><?= esc($item['peran'] ?? '-') ?></td></tr>
    <tr><th class="pe-4">Departemen</th><td><?= esc($item['departemen'] ?? '-') ?></td></tr>
    <tr><th class="pe-4">Poin</th><td><?= (int) ($item['poin'] ?? 0) ?></td></tr>
</table>

<?php if (!empty($item['file_dokumen'])): ?>
    <a href="<?= base_url('library/km/' . $item['id'] . '/download') ?>" class="btn btn-primary">
        <i class="fa-solid fa-download"></i> Download Lampiran
    </a>
<?php endif; ?>
<a href="<?= base_url('library') ?>" class="btn btn-outline-secondary">Kembali ke Library</a>

<?= $this->endSection() ?>
