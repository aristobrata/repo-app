<?php
/**
 * preview_helper.php - lihat catatan lengkap di README.
 */
if (!function_exists('generate_dokumen_preview')) {
    function generate_dokumen_preview(string $pdfFullPath, string $outputDir, int $jumlahHalaman = 5): array
    {
        $hasil = [];
        if (!extension_loaded('imagick')) {
            log_message('error', 'Ekstensi Imagick tidak aktif. Preview tidak bisa di-generate otomatis.');
            return $hasil;
        }
        try {
            $imagick = new \Imagick();
            $imagick->setResolution(150, 150);
            for ($i = 0; $i < $jumlahHalaman; $i++) {
                $imagick->readImage($pdfFullPath . '[' . $i . ']');
                $imagick->setImageFormat('jpg');
                $imagick->setImageCompressionQuality(80);
                $namaFile = bin2hex(random_bytes(12)) . '_p' . ($i + 1) . '.jpg';
                $fullOutputPath = rtrim($outputDir, '/') . '/' . $namaFile;
                $imagick = tambah_watermark_preview($imagick);
                $imagick->writeImage($fullOutputPath);
                $imagick->clear();
                $hasil[] = ['halaman_ke' => $i + 1, 'file_gambar' => $namaFile];
                $imagick = new \Imagick();
                $imagick->setResolution(150, 150);
            }
        } catch (\ImagickException $e) {
            log_message('info', 'Preview generation stopped: ' . $e->getMessage());
        }
        return $hasil;
    }

    function tambah_watermark_preview(\Imagick $imagick): \Imagick
    {
        $draw = new \ImagickDraw();
        $draw->setFillColor(new \ImagickPixel('rgba(90, 90, 90, 0.12)'));
        $draw->setFontSize(16);
        $draw->setFontWeight(400);
        $draw->setTextAlignment(\Imagick::ALIGN_CENTER);
        $width  = $imagick->getImageWidth();
        $height = $imagick->getImageHeight();
        $teks   = 'INTERNAL PREVIEW ONLY';
        $spacingX = $width / 3;
        $spacingY = $height / 6;
        for ($row = 0; $row < 6; $row++) {
            for ($col = 0; $col < 3; $col++) {
                $x = $spacingX * $col + ($spacingX / 2);
                $y = $spacingY * $row + ($spacingY / 2);
                $imagick->annotateImage($draw, $x, $y, -30, $teks);
            }
        }
        return $imagick;
    }
}

if (!function_exists('hapus_file_dokumen')) {
    function hapus_file_dokumen(string $fileAsli, array $filePreviewList): void
    {
        $originalPath = WRITEPATH . 'uploads/originals/' . $fileAsli;
        if (is_file($originalPath)) { unlink($originalPath); }
        foreach ($filePreviewList as $preview) {
            $previewPath = WRITEPATH . 'uploads/previews/' . $preview['file_gambar'];
            if (is_file($previewPath)) { unlink($previewPath); }
        }
    }
}
