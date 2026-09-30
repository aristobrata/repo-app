<?= $this->extend('layouts/main') ?>

<?php
    // Watermark dinamis (forensik): berisi identitas viewer + waktu akses, di-generate
    // ULANG setiap kali halaman ini dibuka (bukan dibakar permanen ke file gambar).
    // Tujuannya: kalau tetap ada yang screenshot & menyebarkan, ada jejak siapa & kapan
    // yang bisa ditelusuri lewat kombinasi nama+waktu yang tertera di hasil screenshot itu.
    $wmNama  = session()->get('nama') ?? '-';
    $wmEmail = session()->get('email') ?? '-';
    $wmWaktu = date('d-m-Y H:i:s');
    $wmText  = $wmNama . '  ·  ' . $wmEmail . '  ·  ' . $wmWaktu;

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="420" height="220">'
         . '<text x="30" y="120" font-family="Arial" font-size="13" fill="rgba(255,0,0,0.28)" '
         . 'transform="rotate(-30 210 110)">' . htmlspecialchars($wmText) . '</text>'
         . '</svg>';
    $svgDataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
?>

<?= $this->section('styles') ?>
<style>
    .preview-page {
        user-select: none;
        -webkit-user-drag: none;
        pointer-events: none;
        max-width: 100%;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        display: block;
    }
    #preview-wrapper { user-select: none; position: relative; }

    /* Watermark dinamis (nama+email+waktu viewer), tile via CSS, menutupi seluruh
       area yang sedang terlihat di layar (fixed = ikut kemanapun user scroll). */
    #dynamic-watermark {
        position: fixed;
        inset: 0;
        background-image: url('<?= $svgDataUri ?>');
        background-repeat: repeat;
        pointer-events: none;
        z-index: 999;
    }

    /* Deterrent tambahan: blur konten saat window/tab kehilangan fokus.
       CATATAN JUJUR: tidak 100% efektif -- banyak tool screenshot modern
       (termasuk screenshot bawaan HP) tidak memicu event blur sama sekali. */
    body.preview-blurred #preview-wrapper {
        filter: blur(20px);
        transition: filter 0.15s ease;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div id="dynamic-watermark"></div>

<div id="preview-wrapper">
    <div class="alert alert-warning">
        ⚠️ Ini adalah mode <strong>Preview</strong> (<?= count($halaman) ?> halaman pertama). Dokumen lengkap hanya
        dapat diakses melalui prosedur internal yang berlaku. Setiap akses ke halaman ini tercatat
        atas nama <strong><?= esc($wmNama) ?></strong>.
    </div>

    <h5><?= esc($dokumen['judul']) ?></h5>

    <?php foreach ($halaman as $h): ?>
        <img class="preview-page" src="<?= base_url('preview-image/' . $h['file_gambar']) ?>"
             alt="Halaman <?= $h['halaman_ke'] ?>">
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.getElementById('preview-wrapper').addEventListener('contextmenu', e => e.preventDefault());

    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 's')) {
            e.preventDefault();
            alert('Fitur cetak/simpan dinonaktifkan untuk dokumen internal ini.');
        }
        if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i'))) {
            e.preventDefault();
        }
    });

    window.addEventListener('blur', () => document.body.classList.add('preview-blurred'));
    window.addEventListener('focus', () => document.body.classList.remove('preview-blurred'));

    // ======================================================================
    // CATATAN PENTING (JUJUR, bukan basa-basi):
    // Tidak ada cara di web (JavaScript/CSS/HTML) yang bisa MENCEGAH screenshot
    // secara mutlak -- baik di desktop (Print Screen, Snipping Tool, OBS) maupun
    // di HP (screenshot bawaan iOS/Android). Ini keterbatasan platform browser,
    // bukan bug di aplikasi ini.
    //
    // Yang aplikasi ini lakukan sebagai gantinya:
    // 1. Deterrent (mempersulit): disable klik-kanan, print, save, devtools shortcut,
    //    blur saat window kehilangan fokus.
    // 2. Watermark FORENSIK (garis pertahanan utama): watermark dinamis berisi nama,
    //    email, dan waktu akses viewer, di-generate ulang setiap kali halaman dibuka.
    //    Kalau tetap di-screenshot & disebar, watermark ini ikut ter-capture --
    //    sehingga bisa ditelusuri siapa & kapan mengaksesnya.
    // 3. File PDF asli tidak pernah dikirim ke browser di mode preview ini.
    // ======================================================================
</script>
<?= $this->endSection() ?>