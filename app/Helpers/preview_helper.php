<?php
/**
 * preview_helper.php
 *
 * Alur:
 * 1. File PDF asli disimpan di writable/uploads/originals/ (TIDAK bisa diakses langsung dari web).
 * 2. Saat upload, N halaman pertama (default 5) di-convert menjadi gambar JPG menggunakan Imagick.
 * 3. Tiap gambar diberi watermark "INTERNAL PREVIEW ONLY" secara diagonal.
 * 4. Gambar hasil disimpan di writable/uploads/previews/ dan path-nya dicatat di tabel
 *    dokumen_preview_pages agar bisa ditampilkan lewat controller streaming, bukan URL langsung.
 *
 * Requirement server: ekstensi PHP Imagick + Ghostscript terinstall
 * (di XAMPP Windows: aktifkan php_imagick.dll di php.ini, install Ghostscript terpisah).
 *
 * Jika Imagick tidak tersedia, gunakan library alternatif seperti `spatie/pdf-to-image`
 * (juga bergantung pada Imagick + Ghostscript) atau layanan konversi eksternal.
 */

if (!function_exists('generate_dokumen_preview')) {
    /**
     * @param string $pdfFullPath  path absolut file PDF asli
     * @param string $outputDir    writable/uploads/previews
     * @param int    $jumlahHalaman jumlah halaman yang di-convert (default 5)
     * @return array daftar nama file gambar hasil convert, urut sesuai halaman
     */
    function generate_dokumen_preview(string $pdfFullPath, string $outputDir, int $jumlahHalaman = 5): array
    {
        $hasil = [];

        if (!extension_loaded('imagick')) {
            log_message('error', 'Ekstensi Imagick tidak aktif. Preview tidak bisa di-generate otomatis.');
            return $hasil;
        }

        try {
            $imagick = new \Imagick();
            $imagick->setResolution(150, 150); // resolusi cukup untuk preview, tidak terlalu berat

            for ($i = 0; $i < $jumlahHalaman; $i++) {
                // Format "path[0]" artinya ambil halaman ke-0 (index dimulai dari 0) dari PDF
                $imagick->readImage($pdfFullPath . '[' . $i . ']');
                $imagick->setImageFormat('jpg');
                $imagick->setImageCompressionQuality(80);

                $namaFile = bin2hex(random_bytes(12)) . '_p' . ($i + 1) . '.jpg';
                $fullOutputPath = rtrim($outputDir, '/') . '/' . $namaFile;

                // Tambahkan watermark sebelum disimpan
                $imagick = tambah_watermark_preview($imagick);

                $imagick->writeImage($fullOutputPath);
                $imagick->clear();

                $hasil[] = ['halaman_ke' => $i + 1, 'file_gambar' => $namaFile];

                // Re-inisialisasi imagick object untuk halaman berikutnya
                $imagick = new \Imagick();
                $imagick->setResolution(150, 150);
            }
        } catch (\ImagickException $e) {
            // Biasanya terjadi jika jumlah halaman PDF < $jumlahHalaman -> berhenti lebih awal, itu wajar
            log_message('info', 'Preview generation stopped: ' . $e->getMessage());
        }

        return $hasil;
    }

    /**
     * Menambahkan watermark teks "INTERNAL PREVIEW ONLY" secara diagonal & berulang (tiled)
     * di atas gambar halaman preview.
     */
    function tambah_watermark_preview(\Imagick $imagick): \Imagick
    {
        $draw = new \ImagickDraw();
        $draw->setFillColor(new \ImagickPixel('rgba(255, 0, 0, 0.35)'));
        $draw->setFontSize(36);
        $draw->setFontWeight(700);
        $draw->setTextAlignment(\Imagick::ALIGN_CENTER);

        $width  = $imagick->getImageWidth();
        $height = $imagick->getImageHeight();

        // Watermark diagonal berulang di beberapa titik agar sulit di-crop
        $teks = 'INTERNAL PREVIEW ONLY';
        $positions = [
            [$width * 0.25, $height * 0.25],
            [$width * 0.75, $height * 0.5],
            [$width * 0.25, $height * 0.75],
        ];

        foreach ($positions as [$x, $y]) {
            $imagick->annotateImage($draw, $x, $y, -30, $teks);
        }

        return $imagick;
    }
}

if (!function_exists('hapus_file_dokumen')) {
    /**
     * Hapus file asli + seluruh file preview terkait dokumen (dipanggil saat admin hapus dokumen).
     */
    function hapus_file_dokumen(string $fileAsli, array $filePreviewList): void
    {
        $originalPath = WRITEPATH . 'uploads/originals/' . $fileAsli;
        if (is_file($originalPath)) {
            unlink($originalPath);
        }

        foreach ($filePreviewList as $preview) {
            $previewPath = WRITEPATH . 'uploads/previews/' . $preview['file_gambar'];
            if (is_file($previewPath)) {
                unlink($previewPath);
            }
        }
    }
}
