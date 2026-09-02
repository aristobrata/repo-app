<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Kontrol Visibilitas Halaman: <?= esc($dokumen['judul']) ?></h4>
<p class="text-muted">Hapus halaman tertentu dari preview jika dianggap terlalu sensitif untuk ditampilkan.</p>

<div class="row">
    <?php foreach ($halaman as $h): ?>
        <div class="col-md-3 mb-3">
            <div class="card">
                <img src="<?= base_url('preview-image/' . $h['file_gambar']) ?>" class="card-img-top">
                <div class="card-body text-center">
                    <p class="mb-2">Halaman <?= $h['halaman_ke'] ?></p>
                    <form action="<?= base_url('admin/dokumen/halaman/' . $h['id'] . '/hapus') ?>" method="post" onsubmit="return confirm('Hapus halaman ini dari preview?')">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-danger">Hapus dari Preview</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>
