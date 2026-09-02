<?php

namespace App\Models;

use CodeIgniter\Model;

class DokumenModel extends Model
{
    protected $table            = 'dokumen';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'kategori_id', 'judul', 'penulis', 'tahun', 'kata_kunci', 'abstrak',
        'nomor_dokumen', 'tanggal_berlaku', 'file_asli', 'jumlah_halaman',
        'halaman_preview', 'cover_thumbnail', 'ukuran_file', 'status_preview',
        'jumlah_view', 'uploaded_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'judul'       => 'required|min_length[3]',
        'penulis'     => 'required',
        'tahun'       => 'required|numeric',
        'kategori_id' => 'required|numeric',
    ];

    /**
     * Filter & Pencarian Cerdas.
     * $filters bisa berisi: keyword, judul, penulis, tahun, kategori_id, sort
     */
    public function searchDokumen(array $filters = [])
    {
        $builder = $this->select('dokumen.*, kategori_dokumen.nama as kategori_nama')
            ->join('kategori_dokumen', 'kategori_dokumen.id = dokumen.kategori_id');

        if (!empty($filters['keyword'])) {
            // full-text search di judul, penulis, kata_kunci, abstrak
            $builder->where(
                "MATCH(dokumen.judul, dokumen.penulis, dokumen.kata_kunci, dokumen.abstrak) AGAINST ('{$this->db->escapeLikeString($filters['keyword'])}' IN NATURAL LANGUAGE MODE)",
                null,
                false
            );
        }

        if (!empty($filters['judul'])) {
            $builder->like('dokumen.judul', $filters['judul']);
        }

        if (!empty($filters['penulis'])) {
            $builder->like('dokumen.penulis', $filters['penulis']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('dokumen.tahun', $filters['tahun']);
        }

        if (!empty($filters['kategori_id'])) {
            $builder->where('dokumen.kategori_id', $filters['kategori_id']);
        }

        switch ($filters['sort'] ?? 'terbaru') {
            case 'terpopuler':
                $builder->orderBy('dokumen.jumlah_view', 'DESC');
                break;
            case 'az':
                $builder->orderBy('dokumen.judul', 'ASC');
                break;
            default:
                $builder->orderBy('dokumen.created_at', 'DESC');
        }

        return $builder;
    }

    public function incrementView(int $id)
    {
        $this->set('jumlah_view', 'jumlah_view + 1', false)->where('id', $id)->update();
    }

    public function topDilihat(int $limit = 10)
    {
        return $this->orderBy('jumlah_view', 'DESC')->findAll($limit);
    }

    public function jumlahPerKategori()
    {
        return $this->select('kategori_dokumen.nama as kategori, COUNT(dokumen.id) as total')
            ->join('kategori_dokumen', 'kategori_dokumen.id = dokumen.kategori_id')
            ->groupBy('kategori_dokumen.id')
            ->findAll();
    }
}
