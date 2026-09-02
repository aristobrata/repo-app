<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Kategori: <?= esc($kategori['nama']) ?></h4>

<div class="row">
    <?php foreach ($dokumen as $d): ?>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <?php if (!empty($d['cover_thumbnail'])): ?>
                    <img src="<?= base_url('cover-image/' . $d['cover_thumbnail']) ?>" class="card-img-top" style="height:160px; object-fit:cover;">
                <?php endif; ?>
                <div class="card-body">
                    <h6 class="card-title"><?= esc($d['judul']) ?></h6>
                    <p class="small text-muted"><?= esc($d['penulis']) ?> · <?= esc($d['tahun']) ?></p>
                    <a href="<?= base_url('dokumen/' . $d['id']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?= $pager->links('dokumen', 'default_full') ?>

<?= $this->endSection() ?>
