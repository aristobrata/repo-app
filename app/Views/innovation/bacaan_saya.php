<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3">Daftar Bacaan Saya</h4>
<?php if (empty($daftar)): ?>
    <p class="text-muted">Belum ada yang Anda simpan.</p>
<?php else: ?>
<div class="table-responsive">
<table class="table table-striped align-middle">
    <thead><tr><th>Tipe</th><th>Judul</th><th>Dibuat Oleh</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($daftar as $item): ?>
        <?php
            $url = $item['tipe'] === 'inovasi' ? base_url('inovasi/' . $item['id']) : base_url('pengetahuan/' . $item['id']);
            $nama = $item['tipe'] === 'inovasi' ? $item['nama_karyawan'] : $item['nama_penulis'];
        ?>
        <tr>
            <td><span class="badge bg-<?= $item['tipe'] === 'inovasi' ? 'success' : 'info' ?>"><?= $item['tipe'] === 'inovasi' ? 'Inovasi' : 'Knowledge' ?></span></td>
            <td><?= esc($item['judul']) ?></td>
            <td><?= esc($nama) ?></td>
            <td><a href="<?= $url ?>" class="btn btn-sm btn-outline-primary">Baca</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
<?= $this->endSection() ?>
