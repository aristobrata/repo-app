<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">Master Karyawan Inovasi</h4>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/inovasi-karyawan/tambah') ?>" class="btn btn-primary">+ Tambah Karyawan</a>
        <a href="<?= base_url('admin/inovasi-karyawan/import') ?>" class="btn btn-outline-primary">⬆ Import Excel</a>
        <a href="<?= base_url('admin/inovasi-karyawan/export') . '?' . http_build_query(array_filter($filters ?? [])) ?>" class="btn btn-outline-success">📊 Export Excel</a>
    </div>
</div>
<?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>
<?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>

<form method="get" class="row g-2 mb-3">
    <input type="hidden" name="per_page" value="<?= per_page_from_request('per_page') ?>">
    <div class="col-md-4"><input type="text" name="keyword" class="form-control" placeholder="Cari nama / Pers No / ID Number" value="<?= esc($filters['keyword'] ?? '') ?>"></div>
    <div class="col-md-4">
        <select name="departemen" class="form-select">
            <option value="">Semua Departemen</option>
            <?php foreach ($departemen as $d): ?>
                <option value="<?= esc($d) ?>" <?= ($filters['departemen'] ?? '') === $d ? 'selected' : '' ?>><?= esc($d) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2"><input type="text" name="band" class="form-control" placeholder="Band (I, II, ...)" value="<?= esc($filters['band'] ?? '') ?>"></div>
    <div class="col-md-2"><button class="btn btn-secondary w-100">Filter</button></div>
</form>

<?= per_page_bar('inovasi_karyawan', $pager) ?>
<div class="table-responsive">
<table class="table table-striped table-sm align-middle">
    <thead><tr><th>Pers/No</th><th>ID Number</th><th>Nama</th><th>Band</th><th>Jabatan</th><th>Gender</th><th>Departemen</th><th>Unit Kerja</th><th>Seksi</th><th style="width:130px">Aksi</th></tr></thead>
    <tbody>
    <?php if (empty($karyawan)): ?>
        <tr><td colspan="10" class="text-center text-muted py-4">Belum ada data. Gunakan <strong>Import Excel</strong> atau <strong>Tambah Karyawan</strong>.</td></tr>
    <?php endif; ?>
    <?php foreach ($karyawan as $k): ?>
        <tr>
            <td><?= esc($k['perner']) ?></td><td><?= esc($k['id_number']) ?></td><td><?= esc($k['nama']) ?></td>
            <td><?= esc($k['band']) ?></td><td class="small"><?= esc($k['jabatan']) ?></td><td class="small"><?= esc($k['gender']) ?></td>
            <td class="small"><?= esc($k['departemen']) ?></td><td class="small"><?= esc($k['nama_unit_kerja']) ?></td><td class="small"><?= esc($k['seksi']) ?></td>
            <td>
                <a href="<?= base_url('admin/inovasi-karyawan/' . $k['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="<?= base_url('admin/inovasi-karyawan/' . $k['id'] . '/hapus') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus karyawan ini?')">
                    <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?= $pager->links('inovasi_karyawan', 'pager/custom') ?>
<?= $this->endSection() ?>
