<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">Kompetisi Inovasi</h4>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/kompetisi/tambah') ?>" class="btn btn-primary">+ Tambah Kompetisi</a>
        <a href="<?= base_url('admin/kompetisi/import') ?>" class="btn btn-outline-secondary">📥 Import Excel</a>
        <a href="<?= base_url('admin/kompetisi/export') . '?' . http_build_query(array_filter($filters ?? [])) ?>" class="btn btn-outline-success">📊 Export Excel</a>
    </div>
</div>
<?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>
<?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>

<form method="get" class="row g-2 mb-3">
    <input type="hidden" name="per_page" value="<?= per_page_from_request('per_page') ?>">
    <div class="col-md-4"><input type="text" name="keyword" class="form-control" placeholder="Cari judul / tim / event..." value="<?= esc($filters['keyword'] ?? '') ?>"></div>
    <div class="col-md-2">
        <select name="jenis" class="form-select"><option value="">Semua Penghargaan</option>
            <?php foreach ($opsi_jenis as $o): ?><option value="<?= esc($o) ?>" <?= ($filters['jenis'] ?? '') === $o ? 'selected' : '' ?>><?= esc($o) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="event" class="form-select"><option value="">Semua Event</option>
            <?php foreach ($opsi_event as $o): ?><option value="<?= esc($o) ?>" <?= ($filters['event'] ?? '') === $o ? 'selected' : '' ?>><?= esc($o) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="tahun" class="form-select"><option value="">Semua Tahun</option>
            <?php foreach (array_reverse($opsi_tahun) as $o): ?><option value="<?= esc($o) ?>" <?= ($filters['tahun'] ?? '') === $o ? 'selected' : '' ?>><?= esc($o) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 d-flex gap-1">
        <button class="btn btn-secondary flex-fill">Cari</button>
        <?php if (array_filter($filters ?? [])): ?><a href="<?= base_url('admin/kompetisi') ?>" class="btn btn-outline-secondary">Reset</a><?php endif; ?>
    </div>
</form>

<?= per_page_bar('kompetisi', $pager) ?>
<div class="table-responsive">
<table class="table table-striped table-sm align-middle">
    <thead><tr><th style="width:60px">Sampul</th><th>Judul Inovasi</th><th>Tim</th><th>Penghargaan</th><th>Event</th><th>Penyelenggara</th><th>Tanggal</th><th>Personil</th><th>Dok.</th><th style="width:130px">Aksi</th></tr></thead>
    <tbody>
    <?php if (empty($kompetisi)): ?><tr><td colspan="10" class="text-center text-muted py-4">Belum ada data. Gunakan <strong>Import Excel</strong> atau <strong>Tambah Kompetisi</strong>.</td></tr><?php endif; ?>
    <?php foreach ($kompetisi as $k): ?>
        <tr>
            <td><img src="<?= !empty($k['cover_thumbnail']) ? base_url('library-image/' . $k['cover_thumbnail']) : base_url('assets/img/no-cover.svg') ?>" style="width:48px;height:36px;object-fit:cover;background:#E2E8F0;border-radius:4px;"></td>
            <td style="max-width:380px"><?= esc($k['judul_inovasi']) ?></td>
            <td><?= esc($k['nama_tim']) ?></td>
            <td><span class="badge bg-warning text-dark"><?= esc($k['jenis_penghargaan']) ?></span></td>
            <td><?= esc($k['event_kompetisi']) ?></td>
            <td class="small"><?= esc($k['penyelenggara']) ?></td>
            <td class="small text-nowrap"><?= esc($k['tanggal_penghargaan']) ?></td>
            <td><?= (int) ($jumlah[$k['id']] ?? 0) ?></td>
            <td class="small"><?= !empty($k['file_dokumen']) ? '📎' : '' ?><?= !empty($k['link_dokumen']) ? '🔗' : '' ?></td>
            <td>
                <a href="<?= base_url('admin/kompetisi/' . $k['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="<?= base_url('admin/kompetisi/' . $k['id'] . '/hapus') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus kompetisi ini beserta file dan daftar personilnya?')">
                    <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?= $pager->links('kompetisi', 'pager/custom') ?>
<?= $this->endSection() ?>
