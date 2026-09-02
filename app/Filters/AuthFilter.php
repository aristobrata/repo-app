<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Memastikan user sudah login sebelum mengakses route yang dilindungi.
 * Daftarkan di app/Config/Filters.php dengan alias 'auth'.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($session->get('status') !== 'aktif') {
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Akun Anda dinonaktifkan. Hubungi admin.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // tidak ada aksi setelah request
    }
}
