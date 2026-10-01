<?php
namespace App\Controllers;
use App\Models\InovasiModel;
use App\Models\KnowledgeModel;
use App\Models\InovasiBookmarkModel;
use App\Models\KnowledgeBookmarkModel;

/**
 * Listing GABUNGAN Inovasi + Knowledge Management dalam satu tabel (sesuai permintaan:
 * "pada repository nanti inovasi dan knowledge management di gabung saja"). Kolom "Tipe"
 * membedakan asal konten; link detail mengarah ke controller masing-masing (Innovation/Knowledge)
 * karena struktur datanya beda, tapi tampilan listing-nya menyatu.
 */
class HubController extends BaseController
{
    protected InovasiModel $inovasiModel;
    protected KnowledgeModel $knowledgeModel;

    public function __construct()
    {
        $this->inovasiModel   = new InovasiModel();
        $this->knowledgeModel = new KnowledgeModel();
    }

    public function index()
    {
        $filters = $this->request->getGet(['keyword', 'divisi', 'tipe']);

        $inovasi = $this->inovasiModel->select("inovasi.id, inovasi.judul, inovasi.divisi, inovasi.status,
                inovasi.jumlah_like, inovasi.jumlah_view, inovasi.created_at, users.nama as nama_pembuat, 'inovasi' as tipe")
            ->join('users', 'users.id = inovasi.karyawan_id');

        $knowledge = $this->knowledgeModel->select("knowledge_hub.id, knowledge_hub.judul, knowledge_hub.divisi, knowledge_hub.status,
                knowledge_hub.jumlah_like, knowledge_hub.jumlah_view, knowledge_hub.created_at, users.nama as nama_pembuat, 'knowledge' as tipe")
            ->join('users', 'users.id = knowledge_hub.penulis_id')
            ->where('knowledge_hub.status', 'dipublikasikan');

        if (!empty($filters['divisi'])) {
            $inovasi->where('inovasi.divisi', $filters['divisi']);
            $knowledge->where('knowledge_hub.divisi', $filters['divisi']);
        }
        if (!empty($filters['keyword'])) {
            $inovasi->like('inovasi.judul', $filters['keyword']);
            $knowledge->like('knowledge_hub.judul', $filters['keyword']);
        }

        $daftarInovasi   = ($filters['tipe'] ?? '') === 'knowledge' ? [] : $inovasi->findAll();
        $daftarKnowledge = ($filters['tipe'] ?? '') === 'inovasi' ? [] : $knowledge->findAll();

        $gabungan = array_merge($daftarInovasi, $daftarKnowledge);
        usort($gabungan, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));

        // Pagination manual sederhana (karena hasil gabungan dari 2 query, bukan 1 query builder)
        $perPage     = 15;
        $currentPage = (int) ($this->request->getGet('page') ?? 1);
        $totalItem   = count($gabungan);
        $totalPage   = (int) ceil($totalItem / $perPage);
        $items       = array_slice($gabungan, ($currentPage - 1) * $perPage, $perPage);

        return view('innovation/index', [
            'items'       => $items,
            'filters'     => $filters,
            'currentPage' => $currentPage,
            'totalPage'   => $totalPage,
        ]);
    }

    /** Daftar Bacaan Saya - GABUNGAN bookmark Inovasi + Knowledge */
    public function bacaanSaya()
    {
        $userId = session()->get('user_id');
        $inovasiBookmarkModel   = new InovasiBookmarkModel();
        $knowledgeBookmarkModel = new KnowledgeBookmarkModel();

        $inovasi   = array_map(fn($i) => $i + ['tipe' => 'inovasi'], $inovasiBookmarkModel->daftarBacaan($userId));
        $knowledge = array_map(fn($k) => $k + ['tipe' => 'knowledge', 'nama_karyawan' => $k['nama_penulis']], $knowledgeBookmarkModel->daftarBacaan($userId));

        $gabungan = array_merge($inovasi, $knowledge);

        return view('innovation/bacaan_saya', ['daftar' => $gabungan]);
    }
}
