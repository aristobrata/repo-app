<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Daftar Bacaan Saya</h4>

<?php if (empty($daftar)): ?>
    <p class="text-muted">Belum ada inovasi yang Anda simpan.</p>
<?php else: ?>
    <div class="row">
        <?php foreach ($daftar as $i): ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6><?= esc($i['judul']) ?></h6>
                        <p class="small text-muted">Oleh: <?= esc($i['nama_karyawan']) ?></p>
                        <a href="<?= base_url('inovasi/' . $i['id']) ?>" class="btn btn-sm btn-outline-primary">Baca</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
