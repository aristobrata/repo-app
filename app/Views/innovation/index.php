<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Inovasi & Knowledge Hub</h4>
    <a href="<?= base_url('inovasi/bacaan-saya') ?>" class="btn btn-outline-secondary btn-sm">📑 Daftar Bacaan Saya</a>
</div>

<form method="get" class="row g-2 mb-4">
    <div class="col-md-3">
        <input type="text" name="keyword" class="form-control" placeholder="Cari judul..." value="<?= esc($filters['keyword'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <input type="text" name="divisi" class="form-control" placeholder="Divisi" value="<?= esc($filters['divisi'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select name="tipe" class="form-select">
            <option value="">Semua Tipe</option>
            <option value="inovasi" <?= ($filters['tipe'] ?? '') === 'inovasi' ? 'selected' : '' ?>>Inovasi</option>
            <option value="knowledge" <?= ($filters['tipe'] ?? '') === 'knowledge' ? 'selected' : '' ?>>Knowledge</option>
        </select>
    </div>
    <div class="col-md-3"><button class="btn btn-secondary w-100">Filter</button></div>
</form>

<div class="table-responsive">
<table class="table table-striped table-hover align-middle">
    <thead>
    <tr>
        <th>Tipe</th>
        <th>Judul</th>
        <th>Dibuat Oleh</th>
        <th>Divisi</th>
        <th>Status</th>
        <th>👍 Like</th>
        <th>👁 Views</th>
        <th>Tanggal</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($items)): ?>
        <tr><td colspan="9" class="text-center text-muted py-4">Belum ada konten.</td></tr>
    <?php endif; ?>
    <?php foreach ($items as $item): ?>
        <?php $url = $item['tipe'] === 'inovasi' ? base_url('inovasi/' . $item['id']) : base_url('pengetahuan/' . $item['id']); ?>
        <tr>
            <td><span class="badge bg-<?= $item['tipe'] === 'inovasi' ? 'success' : 'info' ?>"><?= $item['tipe'] === 'inovasi' ? 'Inovasi' : 'Knowledge' ?></span></td>
            <td><a href="<?= $url ?>"><?= esc($item['judul']) ?></a></td>
            <td><?= esc($item['nama_pembuat']) ?></td>
            <td><?= esc($item['divisi']) ?></td>
            <td><span class="badge bg-secondary"><?= esc($item['status']) ?></span></td>
            <td><?= (int) $item['jumlah_like'] ?></td>
            <td><?= (int) $item['jumlah_view'] ?></td>
            <td class="small text-muted"><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
            <td><a href="<?= $url ?>" class="btn btn-sm btn-outline-primary">Lihat</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php if ($totalPage > 1): ?>
<nav>
    <ul class="pagination">
        <?php for ($p = 1; $p <= $totalPage; $p++): ?>
            <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $p ?>&keyword=<?= esc($filters['keyword'] ?? '') ?>&divisi=<?= esc($filters['divisi'] ?? '') ?>&tipe=<?= esc($filters['tipe'] ?? '') ?>"><?= $p ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?= $this->endSection() ?>
