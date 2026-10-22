<?php
namespace App\Controllers;
use App\Models\InovasiModel;
use App\Models\InovasiAnggotaTimModel;
use App\Models\InovasiLampiranModel;
use App\Models\InovasiLikeModel;
use App\Models\KmAktivitasModel;
use App\Models\KompetisiInovasiModel;
use App\Models\ActivityLogModel;

class InnovationController extends BaseController
{
    protected InovasiModel $inovasiModel;
    protected InovasiAnggotaTimModel $timModel;
    protected InovasiLampiranModel $lampiranModel;
    protected InovasiLikeModel $likeModel;
    protected KmAktivitasModel $kmModel;
    protected KompetisiInovasiModel $kompetisiModel;
    protected ActivityLogModel $logModel;

    public function __construct()
    {
        $this->inovasiModel  = new InovasiModel();
        $this->timModel      = new InovasiAnggotaTimModel();
        $this->lampiranModel = new InovasiLampiranModel();
        $this->likeModel     = new InovasiLikeModel();
        $this->kmModel       = new KmAktivitasModel();
        $this->kompetisiModel = new KompetisiInovasiModel();
        $this->logModel      = new ActivityLogModel();
    }

    /**
     * Modul "Inovasi & KM": menampilkan dua direktori terpisah dalam satu
     * halaman (tab) -- direktori Inovasi yang sudah ada, ditambah direktori
     * Knowledge Management. Masing-masing punya filter & pagination sendiri
     * (group pager 'inovasi' dan 'km') agar tidak saling bentrok.
     */
    public function index()
    {
        $filters = $this->request->getGet(['keyword', 'kategori', 'tahun', 'dept']);
        $inovasi = $this->inovasiModel->daftar($filters)->paginate(per_page_from_request(), 'inovasi');

        $kmGet       = $this->request->getGet(['km_keyword', 'km_pillar', 'km_bulan', 'km_tahun']);
        $kmFilters   = [
            'keyword' => $kmGet['km_keyword'] ?? null,
            'pillar'  => $kmGet['km_pillar'] ?? null,
            'bulan'   => $kmGet['km_bulan'] ?? null,
            'tahun'   => $kmGet['km_tahun'] ?? null,
        ];
        $kmAktivitas = $this->kmModel->daftar($kmFilters)->paginate(per_page_from_request('km_per_page'), 'km');

        // Direktori Kompetisi: filter berprefiks kp_ dan pager/group sendiri supaya tidak bentrok dengan tab lain
        $kpGet     = $this->request->getGet(['kp_keyword', 'kp_jenis', 'kp_event', 'kp_tahun']);
        $kpFilters = [
            'keyword' => $kpGet['kp_keyword'] ?? null,
            'jenis'   => $kpGet['kp_jenis'] ?? null,
            'event'   => $kpGet['kp_event'] ?? null,
            'tahun'   => $kpGet['kp_tahun'] ?? null,
        ];
        $kompetisi = $this->kompetisiModel->daftar($kpFilters)->paginate(per_page_from_request('kompetisi_per_page'), 'kompetisi');
        $kompetisiPager = $this->kompetisiModel->pager;
        $jumlahPersonil = [];
        if ($kompetisi) {
            $rows = (new \App\Models\KompetisiAnggotaModel())->select('kompetisi_id, COUNT(id) as total')
                ->whereIn('kompetisi_id', array_column($kompetisi, 'id'))->groupBy('kompetisi_id')->findAll();
            foreach ($rows as $r) { $jumlahPersonil[$r['kompetisi_id']] = (int) $r['total']; }
        }

        $tabGet = $this->request->getGet('tab');
        $tab    = in_array($tabGet, ['km', 'kompetisi'], true) ? $tabGet : 'inovasi';

        return view('innovation/index', [
            'inovasi'     => $inovasi, 'pager' => $this->inovasiModel->pager, 'filters' => $filters,
            'kmAktivitas' => $kmAktivitas, 'kmPager' => $this->kmModel->pager, 'kmGet' => $kmGet,
            'kompetisi'   => $kompetisi, 'kompetisiPager' => $kompetisiPager, 'kpGet' => $kpGet, 'jumlahPersonil' => $jumlahPersonil,
            'opsiJenis'   => $this->kompetisiModel->nilaiUnik('jenis_penghargaan'),
            'opsiEvent'   => $this->kompetisiModel->nilaiUnik('event_kompetisi'),
            'opsiTahun'   => array_reverse($this->kompetisiModel->nilaiUnik('tahun')),
            'tab'         => $tab,
        ]);
    }

    public function detail(int $id)
    {
        $inovasi = $this->inovasiModel->find($id);
        if (!$inovasi) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }

        $userId = session()->get('user_id');
        $data = [
            'inovasi'    => $inovasi,
            'tim'        => $this->timModel->getFor($id),
            'lampiran'   => $this->lampiranModel->getFor($id),
            'sudah_like' => $this->likeModel->sudahLike($id, $userId),
        ];
        $this->inovasiModel->incrementView($id);
        $this->logModel->catat($userId, 'lihat_inovasi', 'inovasi', $id, $inovasi['judul_inovasi']);
        return view('innovation/detail', $data);
    }

    public function like(int $id)
    {
        $userId  = session()->get('user_id');
        $isLiked = $this->likeModel->toggle($id, $userId);
        $this->inovasiModel->set('jumlah_like', $isLiked ? 'jumlah_like + 1' : 'jumlah_like - 1', false)->where('id', $id)->update();
        $total = $this->inovasiModel->find($id)['jumlah_like'];
        return $this->response->setJSON(['liked' => $isLiked, 'total_like' => $total]);
    }

    public function streamCoverImage(string $filename)
    {
        $path = WRITEPATH . 'uploads/covers/' . basename($filename);
        if (!is_file($path)) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $mime = mime_content_type($path) ?: 'image/jpeg';
        return $this->response->setHeader('Content-Type', $mime)->setHeader('Cache-Control', 'public, max-age=86400')->setBody(file_get_contents($path));
    }

    /** Download file_dokumen (opsional) jika admin pernah upload */
    public function downloadDokumen(int $id)
    {
        $inovasi = $this->inovasiModel->find($id);
        if (!$inovasi || empty($inovasi['file_dokumen'])) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        $path = WRITEPATH . 'uploads/originals/' . $inovasi['file_dokumen'];
        if (!is_file($path)) { throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); }
        return $this->response->download($path, null);
    }
}
