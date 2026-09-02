<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Base Controller aplikasi Internal Digital Repository & Knowledge Center.
 * Semua controller sebaiknya extend class ini (bukan langsung CodeIgniter\Controller)
 * agar konsisten dengan helper & validasi yang di-load global.
 */
class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'preview'];

    protected $session;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = \Config\Services::session();
    }
}
