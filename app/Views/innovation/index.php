<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Inovasi &amp; KM</h4>

<ul class="nav nav-tabs mb-3" id="inovasiKmTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $tab === 'inovasi' ? 'active' : '' ?>" id="tab-inovasi-btn" data-bs-toggle="tab" data-bs-target="#tab-inovasi" type="button" role="tab">Direktori Inovasi</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $tab === 'km' ? 'active' : '' ?>" id="tab-km-btn" data-bs-toggle="tab" data-bs-target="#tab-km" type="button" role="tab">Direktori KM</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $tab === 'kompetisi' ? 'active' : '' ?>" id="tab-kompetisi-btn" data-bs-toggle="tab" data-bs-target="#tab-kompetisi" type="button" role="tab">🏆 Direktori Kompetisi</button>
    </li>
</ul>

<div class="tab-content" id="inovasiKmTabContent">

    <div class="tab-pane fade <?= $tab === 'inovasi' ? 'show active' : '' ?>" id="tab-inovasi" role="tabpanel">

        <form method="get" class="row g-2 mb-4">
            <input type="hidden" name="per_page" value="<?= per_page_from_request('per_page') ?>">
            <input type="hidden" name="tab" value="inovasi">
            <div class="col-md-3"><input type="text" name="keyword" class="form-control" placeholder="Cari judul inovasi..." value="<?= esc($filters['keyword'] ?? '') ?>"></div>
            <div class="col-md-3"><input type="text" name="kategori" class="form-control" placeholder="Kategori (FI, TPP, dll)" value="<?= esc($filters['kategori'] ?? '') ?>"></div>
            <div class="col-md-2"><input type="text" name="tahun" class="form-control" placeholder="Tahun" value="<?= esc($filters['tahun'] ?? '') ?>"></div>
            <div class="col-md-3"><input type="text" name="dept" class="form-control" placeholder="Departemen" value="<?= esc($filters['dept'] ?? '') ?>"></div>
            <div class="col-md-1"><button class="btn btn-primary w-100">Cari</button></div>
        </form>

        <?= per_page_bar('inovasi', $pager) ?>
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
        <?= $pager->links('inovasi', 'pager/custom') ?>

    </div>

    <div class="tab-pane fade <?= $tab === 'km' ? 'show active' : '' ?>" id="tab-km" role="tabpanel">

        <form method="get" class="row g-2 mb-4">
            <input type="hidden" name="km_per_page" value="<?= per_page_from_request('km_per_page') ?>">
            <input type="hidden" name="tab" value="km">
            <div class="col-md-3"><input type="text" name="km_keyword" class="form-control" placeholder="Cari judul event..." value="<?= esc($kmGet['km_keyword'] ?? '') ?>"></div>
            <div class="col-md-3"><input type="text" name="km_pillar" class="form-control" placeholder="Pillar KM" value="<?= esc($kmGet['km_pillar'] ?? '') ?>"></div>
            <div class="col-md-2"><input type="text" name="km_bulan" class="form-control" placeholder="Bulan" value="<?= esc($kmGet['km_bulan'] ?? '') ?>"></div>
            <div class="col-md-2"><input type="text" name="km_tahun" class="form-control" placeholder="Tahun" value="<?= esc($kmGet['km_tahun'] ?? '') ?>"></div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Cari</button></div>
        </form>

        <?= per_page_bar('km', $kmPager, 'km_per_page') ?>
        <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
            <tr><th>Judul Event</th><th>Pillar</th><th>Bulan</th><th>Tahun</th><th>Peserta</th><th>Departemen</th><th>Poin</th><th></th></tr>
            </thead>
            <tbody>
            <?php if (empty($kmAktivitas)): ?><tr><td colspan="8" class="text-center text-muted py-4">Belum ada data KM.</td></tr><?php endif; ?>
            <?php foreach ($kmAktivitas as $k): ?>
                <tr>
                    <td><a href="<?= base_url('library/km/' . $k['id']) ?>"><?= esc($k['judul_event']) ?></a></td>
                    <td><span class="badge bg-info text-dark"><?= esc($k['pillar_km']) ?></span></td>
                    <td><?= esc($k['bulan']) ?></td>
                    <td><?= esc($k['tahun']) ?></td>
                    <td><?= esc($k['nama_peserta']) ?></td>
                    <td class="small"><?= esc($k['departemen']) ?></td>
                    <td><?= (int) $k['poin'] ?></td>
                    <td><a href="<?= base_url('library/km/' . $k['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?= $kmPager->links('km', 'pager/custom') ?>

    </div>

    <div class="tab-pane fade <?= $tab === 'kompetisi' ? 'show active' : '' ?>" id="tab-kompetisi" role="tabpanel">

        <form method="get" class="row g-2 mb-4">
            <input type="hidden" name="kompetisi_per_page" value="<?= per_page_from_request('kompetisi_per_page') ?>">
            <input type="hidden" name="tab" value="kompetisi">
            <div class="col-md-4"><input type="text" name="kp_keyword" class="form-control" placeholder="Cari judul / tim / event..." value="<?= esc($kpGet['kp_keyword'] ?? '') ?>"></div>
            <div class="col-md-2">
                <select name="kp_jenis" class="form-select"><option value="">Semua Penghargaan</option>
                    <?php foreach ($opsiJenis as $o): ?><option value="<?= esc($o) ?>" <?= ($kpGet['kp_jenis'] ?? '') === $o ? 'selected' : '' ?>><?= esc($o) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="kp_event" class="form-select"><option value="">Semua Event</option>
                    <?php foreach ($opsiEvent as $o): ?><option value="<?= esc($o) ?>" <?= ($kpGet['kp_event'] ?? '') === $o ? 'selected' : '' ?>><?= esc($o) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="kp_tahun" class="form-select"><option value="">Semua Tahun</option>
                    <?php foreach ($opsiTahun as $o): ?><option value="<?= esc($o) ?>" <?= ($kpGet['kp_tahun'] ?? '') === $o ? 'selected' : '' ?>><?= esc($o) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Cari</button></div>
        </form>

        <?= per_page_bar('kompetisi', $kompetisiPager, 'kompetisi_per_page') ?>
        <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
            <tr><th>Judul Inovasi</th><th>Tim</th><th>Penghargaan</th><th>Event</th><th>Penyelenggara</th><th>Tanggal</th><th>Personil</th><th></th></tr>
            </thead>
            <tbody>
            <?php if (empty($kompetisi)): ?><tr><td colspan="8" class="text-center text-muted py-4">Belum ada data kompetisi inovasi.</td></tr><?php endif; ?>
            <?php foreach ($kompetisi as $c): ?>
                <tr>
                    <td style="max-width:380px"><a href="<?= base_url('library/kompetisi/' . $c['id']) ?>"><?= esc($c['judul_inovasi']) ?></a></td>
                    <td><?= esc($c['nama_tim']) ?></td>
                    <td><span class="badge bg-warning text-dark"><?= esc($c['jenis_penghargaan']) ?></span></td>
                    <td><?= esc($c['event_kompetisi']) ?></td>
                    <td class="small"><?= esc($c['penyelenggara']) ?></td>
                    <td class="small text-nowrap"><?= esc($c['tanggal_penghargaan']) ?></td>
                    <td><?= (int) ($jumlahPersonil[$c['id']] ?? 0) ?></td>
                    <td><a href="<?= base_url('library/kompetisi/' . $c['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?= $kompetisiPager->links('kompetisi', 'pager/custom') ?>

    </div>

</div>

<?= $this->endSection() ?>
