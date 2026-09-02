<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Selamat datang, <?= esc(session()->get('nama')) ?> 👋</h4>

<div class="row mb-4">
    <div class="col-md-6">
        <h6>Dokumen Terbaru</h6>
        <ul class="list-group">
            <?php foreach ($dokumen_terbaru as $d): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <a href="<?= base_url('dokumen/' . $d['id']) ?>"><?= esc($d['judul']) ?></a>
                    <span class="text-muted small"><?= esc($d['tahun']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-md-6">
        <h6>Inovasi Terbaru</h6>
        <ul class="list-group">
            <?php foreach ($inovasi_terbaru as $i): ?>
                <li class="list-group-item">
                    <a href="<?= base_url('inovasi/' . $i['id']) ?>"><?= esc($i['judul']) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<h6>Dokumen Paling Populer</h6>
<ol>
    <?php foreach ($dokumen_populer as $d): ?>
        <li><?= esc($d['judul']) ?> — <?= (int) $d['jumlah_view'] ?> views</li>
    <?php endforeach; ?>
</ol>

<?= $this->endSection() ?>
