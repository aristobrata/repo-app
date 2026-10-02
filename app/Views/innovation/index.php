<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Direktori Inovasi</h4>

<form method="get" class="row g-2 mb-4">
    <div class="col-md-3"><input type="text" name="keyword" class="form-control" placeholder="Cari judul inovasi..." value="<?= esc($filters['keyword'] ?? '') ?>"></div>
    <div class="col-md-3"><input type="text" name="kategori" class="form-control" placeholder="Kategori (FI, TPP, dll)" value="<?= esc($filters['kategori'] ?? '') ?>"></div>
    <div class="col-md-2"><input type="text" name="tahun" class="form-control" placeholder="Tahun" value="<?= esc($filters['tahun'] ?? '') ?>"></div>
    <div class="col-md-3"><input type="text" name="dept" class="form-control" placeholder="Departemen" value="<?= esc($filters['dept'] ?? '') ?>"></div>
    <div class="col-md-1"><button class="btn btn-primary w-100">Cari</button></div>
</form>

<div class="table-responsive">
<table class="table table-striped table-hover align-middle">
    <thead>
    <tr><th>Judul Inovasi</th><th>Kategori</th><th>Tim</th><th>Dept</th><th>Tahun</th><th>Status</th><th>Total Benefit</th><th></th></tr>
    </thead>
    <tbody>
    <?php if (empty($inovasi)): ?><tr><td colspan="8" class="text-center text-muted py-4">Belum ada data inovasi.</td></tr><?php endif; ?>
    <?php foreach ($inovasi as $i): ?>
        <tr>
            <td><a href="<?= base_url('inovasi/' . $i['id']) ?>"><?= esc($i['judul_inovasi']) ?></a></td>
            <td><span class="badge bg-secondary"><?= esc($i['kategori_inovasi']) ?></span></td>
            <td><?= esc($i['nama_tim']) ?></td>
            <td class="small"><?= esc($i['unit_dept_area_implementasi']) ?></td>
            <td><?= esc($i['tahun']) ?></td>
            <td><?= esc($i['status_saat_ini']) ?></td>
            <td>Rp <?= number_format($i['total_benefit'] ?? 0, 0, ',', '.') ?></td>
            <td><a href="<?= base_url('inovasi/' . $i['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?= $pager->links('inovasi', 'default_full') ?>
<?= $this->endSection() ?>
