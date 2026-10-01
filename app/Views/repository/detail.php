<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <?php if (!empty($dokumen['cover_thumbnail'])): ?>
            <img src="<?= base_url('cover-image/' . $dokumen['cover_thumbnail']) ?>" class="img-fluid rounded mb-3" style="max-height:280px; object-fit:cover;">
        <?php endif; ?>
        <h4><?= esc($dokumen['judul']) ?></h4>
        <p class="text-muted">
            Penulis: <?= esc($dokumen['penulis']) ?> &middot; Tahun: <?= esc($dokumen['tahun']) ?>
            <?php if (!empty($dokumen['nomor_dokumen'])): ?> &middot; No. Dokumen: <?= esc($dokumen['nomor_dokumen']) ?><?php endif; ?>
        </p>
        <h6>Abstrak</h6>
        <p><?= nl2br(esc($dokumen['abstrak'] ?? '-')) ?></p>
        <?php if (!empty($dokumen['kata_kunci'])): ?><p><strong>Kata kunci:</strong> <?= esc($dokumen['kata_kunci']) ?></p><?php endif; ?>
        <div class="mt-3">
            <?php if ($dokumen['status_preview'] === 'ready'): ?>
                <a href="<?= base_url('dokumen/' . $dokumen['id'] . '/preview') ?>" class="btn btn-primary">📖 Baca Preview (<?= (int) $dokumen['halaman_preview'] ?> halaman)</a>
            <?php else: ?>
                <span class="badge bg-warning text-dark">Preview sedang diproses / tidak tersedia</span>
            <?php endif; ?>
            <?php if (in_array(session()->get('role'), ['admin', 'super_admin'])): ?>
                <a href="<?= base_url('admin/dokumen/' . $dokumen['id'] . '/lihat-lengkap') ?>" class="btn btn-outline-success" target="_blank">📄 Lihat Dokumen Lengkap</a>
                <a href="<?= base_url('admin/dokumen/' . $dokumen['id'] . '/download') ?>" class="btn btn-outline-secondary">⬇️ Download File Asli</a>
                <div class="form-text mt-1">Akses tanpa batas hanya tersedia untuk Admin/Super Admin.</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body small text-muted">
            <p class="mb-1">Ukuran file: <?= number_format(($dokumen['ukuran_file'] ?? 0) / 1024, 1) ?> KB</p>
            <p class="mb-0">Dilihat: <?= (int) $dokumen['jumlah_view'] ?> kali</p>
        </div></div>
    </div>
</div>
<?= $this->endSection() ?>
