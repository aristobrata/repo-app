<?= $this->extend('layouts/main') ?>
<?php
    $wmNama  = session()->get('nama') ?? '-';
    $wmEmail = session()->get('email') ?? '-';
    $wmWaktu = date('d-m-Y H:i:s');
    $wmText  = $wmNama . '  ·  ' . $wmEmail . '  ·  ' . $wmWaktu;
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="420" height="220">'
         . '<text x="30" y="120" font-family="Arial" font-size="13" fill="rgba(255,0,0,0.28)" transform="rotate(-30 210 110)">'
         . htmlspecialchars($wmText) . '</text></svg>';
    $svgDataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
?>
<?= $this->section('styles') ?>
<style>
    .preview-page { user-select: none; -webkit-user-drag: none; pointer-events: none; max-width: 100%; margin-bottom: 1rem; border: 1px solid #ddd; display: block; }
    #preview-wrapper { user-select: none; position: relative; }
    #dynamic-watermark { position: fixed; inset: 0; background-image: url('<?= $svgDataUri ?>'); background-repeat: repeat; pointer-events: none; z-index: 999; }
    body.preview-blurred #preview-wrapper { filter: blur(20px); transition: filter 0.15s ease; }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div id="dynamic-watermark"></div>
<div id="preview-wrapper">
    <div class="alert alert-warning">
        ⚠️ Ini adalah mode <strong>Preview</strong> (<?= count($halaman) ?> halaman pertama). Setiap akses tercatat atas nama <strong><?= esc($wmNama) ?></strong>.
    </div>
    <h5><?= esc($dokumen['judul']) ?></h5>
    <?php foreach ($halaman as $h): ?>
        <img class="preview-page" src="<?= base_url('preview-image/' . $h['file_gambar']) ?>" alt="Halaman <?= $h['halaman_ke'] ?>">
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    document.getElementById('preview-wrapper').addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 's')) { e.preventDefault(); alert('Fitur cetak/simpan dinonaktifkan.'); }
        if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i'))) { e.preventDefault(); }
    });
    window.addEventListener('blur', () => document.body.classList.add('preview-blurred'));
    window.addEventListener('focus', () => document.body.classList.remove('preview-blurred'));
</script>
<?= $this->endSection() ?>
