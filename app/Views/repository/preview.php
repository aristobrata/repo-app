<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    /* Cegah select/drag gambar sebagai deterrent tambahan (bukan proteksi mutlak) */
    .preview-page {
        user-select: none;
        -webkit-user-drag: none;
        pointer-events: none; /* cegah klik kanan "save image as" langsung di elemen img */
        max-width: 100%;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
    }
    #preview-wrapper { user-select: none; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div id="preview-wrapper">
    <div class="alert alert-warning">
        ⚠️ Ini adalah mode <strong>Preview</strong> (<?= count($halaman) ?> halaman pertama). Dokumen lengkap hanya
        dapat diakses melalui prosedur internal yang berlaku.
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
    // Disable klik kanan (context menu) di area preview
    document.getElementById('preview-wrapper').addEventListener('contextmenu', e => e.preventDefault());

    // Disable Ctrl+P (print) & Ctrl+S (save) selama halaman preview terbuka
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 's')) {
            e.preventDefault();
            alert('Fitur cetak/simpan dinonaktifkan untuk dokumen internal ini.');
        }
    });

    // Catatan penting: proteksi client-side ini hanya deterrent, bukan proteksi mutlak
    // (screenshot tetap memungkinkan). Pengamanan utama ada di server: file PDF asli
    // tidak pernah dikirim ke browser pada mode preview, hanya gambar per-halaman.
</script>
<?= $this->endSection() ?>
