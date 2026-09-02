<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Innovation Hub</h4>
    <a href="<?= base_url('inovasi/bacaan-saya') ?>" class="btn btn-outline-secondary btn-sm">📑 Daftar Bacaan Saya</a>
</div>

<form method="get" class="row g-2 mb-4">
    <div class="col-md-3">
        <input type="text" name="divisi" class="form-control" placeholder="Divisi" value="<?= esc($filters['divisi'] ?? '') ?>">
    </div>
    <div class="col-md-2">
        <input type="number" name="tahun" class="form-control" placeholder="Tahun" value="<?= esc($filters['tahun'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="diajukan" <?= ($filters['status'] ?? '') === 'diajukan' ? 'selected' : '' ?>>Diajukan</option>
            <option value="diverifikasi" <?= ($filters['status'] ?? '') === 'diverifikasi' ? 'selected' : '' ?>>Diverifikasi</option>
            <option value="diterapkan" <?= ($filters['status'] ?? '') === 'diterapkan' ? 'selected' : '' ?>>Diterapkan</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-secondary w-100">Filter</button>
    </div>
</form>

<div class="row">
    <?php foreach ($inovasi as $i): ?>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <?php if (!empty($i['foto_ilustrasi'])): ?>
                    <img src="<?= base_url('inovasi-image/' . $i['foto_ilustrasi']) ?>" class="card-img-top" style="height:160px; object-fit:cover;">
                <?php endif; ?>
                <div class="card-body">
                    <span class="badge bg-info text-dark mb-2"><?= esc($i['status']) ?></span>
                    <h6><?= esc($i['judul']) ?></h6>
                    <p class="small text-muted">Oleh: <?= esc($i['nama_karyawan']) ?> · <?= esc($i['divisi']) ?></p>
                    <p class="small">👍 <?= (int) $i['jumlah_like'] ?> &nbsp; 👁 <?= (int) $i['jumlah_view'] ?></p>
                    <a href="<?= base_url('inovasi/' . $i['id']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?= $pager->links('inovasi', 'default_full') ?>

<?= $this->endSection() ?>
