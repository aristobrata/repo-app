<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table      = 'kategori_dokumen';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['nama'];
}
