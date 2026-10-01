<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Selamat datang, <?= esc(session()->get('nama')) ?> 👋</h4>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center border-primary">
            <div class="card-body">
                <h2 class="mb-0"><?= (int) $total_dokumen ?></h2>
                <p class="text-muted mb-0 small">Total Dokumen</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <h2 class="mb-0"><?= (int) $total_inovasi ?></h2>
                <p class="text-muted mb-0 small">Total Inovasi</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-info">
            <div class="card-body">
                <h2 class="mb-0"><?= (int) $total_knowledge ?></h2>
                <p class="text-muted mb-0 small">Knowledge Item</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-secondary">
            <div class="card-body">
                <h2 class="mb-0"><?= (int) $total_user ?></h2>
                <p class="text-muted mb-0 small">User Aktif</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <h6>Dokumen Terbaru</h6>
        <table class="table table-sm table-striped">
            <thead><tr><th>Judul</th><th>Tahun</th></tr></thead>
            <tbody>
            <?php foreach ($dokumen_terbaru as $d): ?>
                <tr>
                    <td><a href="<?= base_url('dokumen/' . $d['id']) ?>"><?= esc($d['judul']) ?></a></td>
                    <td><?= esc($d['tahun']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        <h6>Inovasi & Knowledge Terbaru</h6>
        <table class="table table-sm table-striped">
            <thead><tr><th>Judul</th><th>Tipe</th></tr></thead>
            <tbody>
            <?php foreach ($konten_terbaru as $k): ?>
                <tr>
                    <td>
                        <?php $url = $k['tipe'] === 'inovasi' ? base_url('inovasi/' . $k['id']) : base_url('pengetahuan/' . $k['id']); ?>
                        <a href="<?= $url ?>"><?= esc($k['judul']) ?></a>
                    </td>
                    <td><span class="badge bg-<?= $k['tipe'] === 'inovasi' ? 'success' : 'info' ?>"><?= $k['tipe'] === 'inovasi' ? 'Inovasi' : 'Knowledge' ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <h6>Dokumen Paling Populer</h6>
        <ol>
            <?php foreach ($dokumen_populer as $d): ?>
                <li><?= esc($d['judul']) ?> — <?= (int) $d['jumlah_view'] ?> views</li>
            <?php endforeach; ?>
        </ol>
    </div>
    <?php if (in_array(session()->get('role'), ['admin', 'super_admin'])): ?>
    <div class="col-md-6">
        <h6>Aktivitas Terbaru</h6>
        <table class="table table-sm">
            <tbody>
            <?php foreach ($aktivitas_terbaru as $a): ?>
                <tr>
                    <td class="small text-muted"><?= esc($a['created_at']) ?></td>
                    <td class="small"><?= esc($a['nama_user'] ?? '-') ?></td>
                    <td class="small"><?= esc($a['action']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
