<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Library</h4>
<p class="text-muted small">Kumpulan dokumen dari Inovasi dan Knowledge Management, ditampilkan otomatis di satu tempat.</p>

<form method="get" class="row g-2 mb-4">
    <div class="col-md-5">
        <input type="text" name="keyword" class="form-control" placeholder="Cari judul..." value="<?= esc($filters['keyword'] ?? '') ?>">
    </div>
    <div class="col-md-2">
        <input type="number" name="tahun" class="form-control" placeholder="Tahun" value="<?= esc($filters['tahun'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select name="sumber" class="form-select">
            <option value="">Semua Sumber</option>
            <option value="inovasi" <?= ($filters['sumber'] ?? '') === 'inovasi' ? 'selected' : '' ?>>Inovasi</option>
            <option value="km" <?= ($filters['sumber'] ?? '') === 'km' ? 'selected' : '' ?>>Knowledge Management</option>
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Cari</button></div>
</form>

<div class="row">
    <?php if (empty($items)): ?>
        <div class="col-12 text-center text-muted py-5">Belum ada data.</div>
    <?php endif; ?>

    <?php foreach ($items as $it): ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <img src="<?= !empty($it['cover']) ? base_url('library-image/' . $it['cover']) : base_url('assets/img/no-cover.svg') ?>"
                     class="card-img-top" style="height:160px; object-fit:cover; background:#E2E8F0;">
                <div class="card-body d-flex flex-column">
                    <span class="badge <?= $it['sumber'] === 'inovasi' ? 'bg-primary' : 'bg-info text-dark' ?> mb-2 align-self-start">
                        <?= $it['sumber'] === 'inovasi' ? 'Inovasi' : 'Knowledge Mgmt' ?>
                    </span>
                    <h6 class="card-title"><?= esc($it['judul']) ?></h6>
                    <p class="card-text small text-muted mb-2"><?= esc($it['kategori'] ?? '-') ?> · <?= esc($it['tahun']) ?></p>
                    <div class="mt-auto">
                        <?php if ($it['sumber'] === 'inovasi'): ?>
                            <a href="<?= base_url('inovasi/' . $it['id']) ?>" class="btn btn-sm btn-outline-primary w-100">Lihat Detail</a>
                        <?php else: ?>
                            <a href="<?= base_url('library/km/' . $it['id']) ?>" class="btn btn-sm btn-outline-primary w-100">Lihat Detail</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if ($totalPage > 1): ?>
<nav aria-label="Pagination">
  <ul class="pagination justify-content-center">
    <?php
        $queryTanpaPage = array_filter($filters ?? []);
        $buatLink = static function (int $page) use ($queryTanpaPage) {
            return base_url('library') . '?' . http_build_query(array_merge($queryTanpaPage, ['page' => $page]));
        };
    ?>
    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= $buatLink(max(1, $currentPage - 1)) ?>">Prev</a>
    </li>
    <?php for ($p = max(1, $currentPage - 1); $p <= min($totalPage, $currentPage + 1); $p++): ?>
        <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
            <a class="page-link" href="<?= $buatLink($p) ?>"><?= $p ?></a>
        </li>
    <?php endfor; ?>
    <li class="page-item <?= $currentPage >= $totalPage ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= $buatLink(min($totalPage, $currentPage + 1)) ?>">Next</a>
    </li>
  </ul>
</nav>
<?php endif; ?>

<?= $this->endSection() ?>
