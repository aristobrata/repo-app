<?php

namespace App\Models;

use CodeIgniter\Model;

class DokumenPreviewPageModel extends Model
{
    protected $table         = 'dokumen_preview_pages';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['dokumen_id', 'halaman_ke', 'file_gambar'];
    protected $useTimestamps = false;

    public function getPagesFor(int $dokumenId)
    {
        return $this->where('dokumen_id', $dokumenId)->orderBy('halaman_ke', 'ASC')->findAll();
    }
}
