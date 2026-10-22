<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<img src="<?= !empty($item['cover_thumbnail']) ? base_url('library-image/' . $item['cover_thumbnail']) : base_url('assets/img/no-cover.svg') ?>"
     class="img-fluid rounded mb-3" style="max-height:280px; object-fit:cover; width:100%; background:#E2E8F0;">

<span class="badge bg-warning text-dark mb-2">🏆 <?= esc($item['jenis_penghargaan'] ?: 'Kompetisi Inovasi') ?></span>
<h4><?= esc($item['judul_inovasi']) ?></h4>
<p class="text-muted">
    <?= esc($item['event_kompetisi']) ?>
    <?php if (!empty($item['tanggal_penghargaan'])): ?> · <?= esc(date('d M Y', strtotime($item['tanggal_penghargaan']))) ?><?php endif; ?>
</p>

<table class="table table-sm w-auto">
    <tr><th class="pe-4">Nama Tim</th><td><?= esc($item['nama_tim'] ?: '-') ?></td></tr>
    <tr><th class="pe-4">Jenis Penghargaan</th><td><?= esc($item['jenis_penghargaan'] ?: '-') ?></td></tr>
    <tr><th class="pe-4">Event / Ajang</th><td><?= esc($item['event_kompetisi'] ?: '-') ?></td></tr>
    <tr><th class="pe-4">Penyelenggara</th><td><?= esc($item['penyelenggara'] ?: '-') ?></td></tr>
    <tr><th class="pe-4">Tahun</th><td><?= esc($item['tahun'] ?: '-') ?></td></tr>
</table>

<?php if (!empty($item['keterangan'])): ?><p><?= nl2br(esc($item['keterangan'])) ?></p><?php endif; ?>

<?php if (!empty($item['foto_penghargaan'])): ?>
    <h6 class="mt-4">Dokumentasi Penghargaan</h6>
    <a href="<?= base_url('library/kompetisi/' . $item['id'] . '/foto') ?>" target="_blank">
        <img src="<?= base_url('library/kompetisi/' . $item['id'] . '/foto') ?>" alt="Dokumentasi penghargaan" class="img-fluid rounded border" style="max-height:420px;">
    </a>
<?php endif; ?>

<h6 class="mt-4">Personil (<?= count($anggota) ?>)</h6>
<?php if (empty($anggota)): ?>
    <p class="text-muted small">Belum ada data personil.</p>
<?php else: ?>
    <div class="table-responsive" style="max-width:520px">
    <table class="table table-sm table-striped">
        <thead><tr><th style="width:50px">No</th><th>Nama Karyawan</th><th>NIK</th></tr></thead>
        <tbody>
        <?php foreach ($anggota as $i => $a): ?>
            <tr><td><?= $i + 1 ?></td><td><?= esc($a['nama_karyawan']) ?></td><td><?= esc($a['nik'] ?: '-') ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>

<div class="d-flex gap-2 mt-3 flex-wrap">
    <?php if (!empty($item['file_dokumen'])): ?>
        <a href="<?= base_url('library/kompetisi/' . $item['id'] . '/download') ?>" class="btn btn-primary">Download Dokumen</a>
    <?php endif; ?>
    <?php if (!empty($item['link_dokumen']) && preg_match('#^https?://#i', $item['link_dokumen'])): ?>
        <a href="<?= esc($item['link_dokumen'], 'attr') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary">Buka Link Dokumen</a>
    <?php endif; ?>
    <a href="<?= base_url('library') ?>" class="btn btn-outline-secondary">Kembali ke Library</a>
</div>

<?= $this->endSection() ?>
