<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Library</h4>
<p class="text-muted small">Kumpulan dokumen dari Inovasi, Knowledge Management, dan Kompetisi Inovasi, ditampilkan otomatis di satu tempat.</p>

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
            <option value="kompetisi" <?= ($filters['sumber'] ?? '') === 'kompetisi' ? 'selected' : '' ?>>Kompetisi Inovasi</option>
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Cari</button></div>
</form>

<div class="row">
    <?php if (empty($items)): ?>
        <div class="col-12 text-center text-muted py-5">Belum ada data.</div>
    <?php endif; ?>

    <?php foreach ($items as $it): ?>
        <?php
            $badgeKelas = ['inovasi' => 'bg-primary', 'km' => 'bg-info text-dark', 'kompetisi' => 'bg-warning text-dark'][$it['sumber']] ?? 'bg-secondary';
            $badgeNama  = ['inovasi' => 'Inovasi', 'km' => 'Knowledge Mgmt', 'kompetisi' => '🏆 Kompetisi Inovasi'][$it['sumber']] ?? '';
            $linkDetail = $it['sumber'] === 'inovasi' ? base_url('inovasi/' . $it['id'])
                        : ($it['sumber'] === 'kompetisi' ? base_url('library/kompetisi/' . $it['id']) : base_url('library/km/' . $it['id']));
            $srcCover   = !empty($it['cover']) ? base_url('library-image/' . $it['cover']) : base_url('assets/img/no-cover.svg');
        ?>
        <div class="col-12 col-xl-6 col-xxl-4 mb-4">
            <div class="card h-100 flex-row overflow-hidden">
                <a href="<?= $linkDetail ?>" class="flex-shrink-0 d-block" style="width:130px; background:#E2E8F0;">
                    <img src="<?= $srcCover ?>" alt="Sampul" loading="lazy"
                         style="width:130px; aspect-ratio:3/4; height:100%; min-height:173px; object-fit:contain; display:block;">
                </a>
                <div class="card-body d-flex flex-column" style="min-width:0;">
                    <span class="badge <?= $badgeKelas ?> mb-2 align-self-start"><?= $badgeNama ?></span>
                    <h6 class="card-title" style="display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;"><?= esc($it['judul']) ?></h6>
                    <p class="card-text small text-muted mb-2"><?= esc($it['kategori'] ?? '-') ?> · <?= esc($it['tahun']) ?></p>
                    <div class="mt-auto">
                        <a href="<?= $linkDetail ?>" class="btn btn-sm btn-outline-primary w-100">Lihat Detail</a>
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
