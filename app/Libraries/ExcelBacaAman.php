<?php
namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Pembaca Excel yang hemat memori. Hanya membaca SATU sheet, hanya kolom A..$kolomAkhir,
 * dan maksimal $maksBaris baris, tanpa memuat format/gaya sel. Ini mencegah error
 * "Allowed memory size exhausted" pada file yang "dimensinya" membengkak (misalnya
 * format sel sampai baris 1.048.576 atau kolom XFD) walaupun datanya hanya ratusan baris.
 */
class ExcelBacaAman implements IReadFilter
{
    private int $kolomMaks;
    private int $barisMaks;

    private function __construct(int $kolomMaks, int $barisMaks)
    {
        $this->kolomMaks = $kolomMaks;
        $this->barisMaks = $barisMaks;
    }

    public function readCell($columnAddress, $row, $worksheetName = ''): bool
    {
        return $row <= $this->barisMaks
            && \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnAddress) <= $this->kolomMaks;
    }

    /**
     * @return array<int, array<int, mixed>> baris berindeks 0..n (baris pertama = header),
     *         baris kosong di akhir dibuang.
     */
    public static function baca(string $path, string $namaSheet, string $kolomAkhir = 'J', int $maksBaris = 20000): array
    {
        $kolomMaks = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($kolomAkhir);
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $reader->setReadEmptyCells(false);
        if (method_exists($reader, 'listWorksheetNames')) {
            $daftar = $reader->listWorksheetNames($path);
            $sheet  = in_array($namaSheet, $daftar, true) ? $namaSheet : ($daftar[0] ?? null);
            if ($sheet !== null) { $reader->setLoadSheetsOnly([$sheet]); }
        }
        $reader->setReadFilter(new self($kolomMaks, $maksBaris));
        $spreadsheet = $reader->load($path);
        $ws = $spreadsheet->getActiveSheet();

        $barisTerakhir = min($ws->getHighestDataRow(), $maksBaris);
        $rows = $ws->rangeToArray("A1:{$kolomAkhir}{$barisTerakhir}", null, true, false, false);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        while ($rows && !array_filter(end($rows), fn($v) => $v !== null && trim((string) $v) !== '')) { array_pop($rows); }
        return $rows;
    }
}
