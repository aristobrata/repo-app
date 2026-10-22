<?php
$edit = !empty($kompetisi);
$v = fn(string $f, $d = '') => old($f, $edit ? ($kompetisi[$f] ?? $d) : $d);
$action = $edit ? base_url('admin/kompetisi/' . $kompetisi['id'] . '/update') : base_url('admin/kompetisi');
$barisAnggota = $anggota ?: [['nik' => '', 'nama_karyawan' => '']];
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4 class="mb-3"><?= $edit ? 'Edit' : 'Tambah' ?> Kompetisi Inovasi</h4>
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form action="<?= $action ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3"><label class="form-label">Judul Inovasi *</label><input type="text" name="judul_inovasi" class="form-control" required value="<?= esc($v('judul_inovasi')) ?>"></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Nama Tim</label><input type="text" name="nama_tim" class="form-control" value="<?= esc($v('nama_tim')) ?>"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Jenis Penghargaan</label>
                    <input type="text" name="jenis_penghargaan" list="dl-jenis" class="form-control" placeholder="GOLD, PLATINUM, ..." value="<?= esc($v('jenis_penghargaan')) ?>">
                    <datalist id="dl-jenis"><?php foreach ($opsi_jenis as $o): ?><option value="<?= esc($o) ?>"><?php endforeach; ?></datalist>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Event / Ajang Kompetisi</label>
                    <input type="text" name="event_kompetisi" list="dl-event" class="form-control" placeholder="SPIE, TKMPN, ICQCC, ..." value="<?= esc($v('event_kompetisi')) ?>">
                    <datalist id="dl-event"><?php foreach ($opsi_event as $o): ?><option value="<?= esc($o) ?>"><?php endforeach; ?></datalist>
                </div>
                <div class="col-md-6 mb-3"><label class="form-label">Penyelenggara</label>
                    <input type="text" name="penyelenggara" list="dl-penyelenggara" class="form-control" value="<?= esc($v('penyelenggara')) ?>">
                    <datalist id="dl-penyelenggara"><?php foreach ($opsi_penyelenggara as $o): ?><option value="<?= esc($o) ?>"><?php endforeach; ?></datalist>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Tanggal Penghargaan</label><input type="date" name="tanggal_penghargaan" class="form-control" value="<?= esc($v('tanggal_penghargaan')) ?>"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Tahun</label><input type="text" name="tahun" class="form-control" placeholder="Otomatis dari tanggal jika kosong" value="<?= esc($v('tahun')) ?>"></div>
            </div>
            <div class="mb-3"><label class="form-label">Keterangan</label><textarea name="keterangan" class="form-control" rows="3"><?= esc($v('keterangan')) ?></textarea></div>
            <div class="mb-3"><label class="form-label">Link Dokumen Pendukung (opsional)</label><input type="text" name="link_dokumen" class="form-control" placeholder="https://drive.google.com/..." value="<?= esc($v('link_dokumen')) ?>"></div>

            <h6 class="mt-4">Personil / Peserta</h6>
            <div id="anggota-wrapper">
                <?php foreach ($barisAnggota as $a): ?>
                <div class="row anggota-row mb-2">
                    <div class="col-md-8"><input type="text" name="anggota_nama[]" class="form-control" placeholder="Nama Karyawan" value="<?= esc($a['nama_karyawan'] ?? '') ?>"></div>
                    <div class="col-md-3"><input type="text" name="anggota_nik[]" class="form-control" placeholder="NIK" value="<?= esc($a['nik'] ?? '') ?>"></div>
                    <div class="col-md-1"><button type="button" class="btn btn-outline-danger btn-hapus-baris" title="Hapus baris">&times;</button></div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="btn-tambah-anggota" class="btn btn-sm btn-outline-secondary mb-3">+ Tambah Personil</button>
        </div>

        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">File Dokumen <span class="badge bg-secondary">Tidak Wajib</span></label>
                <input type="file" name="file_dokumen" class="form-control">
                <div class="form-text">PDF, Word, PowerPoint, Excel, ZIP, atau gambar. Maks. 50 MB.</div>
                <?php if ($edit && !empty($kompetisi['file_dokumen'])): ?>
                    <div class="form-text">File saat ini: <a href="<?= base_url('library/kompetisi/' . $kompetisi['id'] . '/download') ?>">unduh</a>
                        &middot; <label><input type="checkbox" name="hapus_file_dokumen" value="1"> hapus file ini</label></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Sampul / Cover <span class="badge bg-secondary">Tidak Wajib</span></label>
                <input type="file" name="cover" class="form-control" accept="image/jpeg,image/png" id="input-cover">
                <div class="form-text">JPG/PNG, maks. 3 MB. Jika kosong, Library memakai gambar placeholder.</div>
                <?php $coverSaatIni = $edit && !empty($kompetisi['cover_thumbnail']) ? base_url('library-image/' . $kompetisi['cover_thumbnail']) : ''; ?>
                <img id="preview-cover" src="<?= $coverSaatIni ?>" class="img-fluid rounded mt-2" style="max-height:160px; <?= $coverSaatIni ? '' : 'display:none;' ?>">
                <?php if ($coverSaatIni): ?><div class="form-text"><label><input type="checkbox" name="hapus_cover" value="1"> hapus sampul ini</label></div><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="mb-3" style="max-width:520px">
        <label class="form-label">Foto Dokumentasi Penghargaan <span class="badge bg-secondary">Tidak Wajib</span></label>
        <input type="file" name="foto_penghargaan" class="form-control" accept="image/jpeg,image/png" id="input-foto">
        <div class="form-text">Foto saat menerima penghargaan / piala / sertifikat. JPG/PNG, maks. 5 MB. Tampil di halaman detail Library.</div>
        <?php $fotoSaatIni = $edit && !empty($kompetisi['foto_penghargaan']) ? base_url('library/kompetisi/' . $kompetisi['id'] . '/foto') : ''; ?>
        <img id="preview-foto" src="<?= $fotoSaatIni ?>" class="img-fluid rounded mt-2" style="max-height:200px; <?= $fotoSaatIni ? '' : 'display:none;' ?>">
        <?php if ($fotoSaatIni): ?><div class="form-text"><label><input type="checkbox" name="hapus_foto_penghargaan" value="1"> hapus foto ini</label></div><?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="<?= base_url('admin/kompetisi') ?>" class="btn btn-outline-secondary">Batal</a>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    const wrapper = document.getElementById('anggota-wrapper');
    document.getElementById('btn-tambah-anggota').addEventListener('click', function () {
        const row = wrapper.querySelector('.anggota-row').cloneNode(true);
        row.querySelectorAll('input').forEach(function (i) { i.value = ''; });
        wrapper.appendChild(row);
    });
    wrapper.addEventListener('click', function (e) {
        if (!e.target.classList.contains('btn-hapus-baris')) { return; }
        const rows = wrapper.querySelectorAll('.anggota-row');
        if (rows.length > 1) { e.target.closest('.anggota-row').remove(); }
        else { rows[0].querySelectorAll('input').forEach(function (i) { i.value = ''; }); }
    });
    document.getElementById('input-foto').addEventListener('change', function () {
        const f = this.files && this.files[0];
        const img = document.getElementById('preview-foto');
        if (f) { img.src = URL.createObjectURL(f); img.style.display = ''; }
    });
    document.getElementById('input-cover').addEventListener('change', function () {
        const f = this.files && this.files[0];
        const img = document.getElementById('preview-cover');
        if (f) { img.src = URL.createObjectURL(f); img.style.display = ''; }
    });
})();
</script>
<?= $this->endSection() ?>
