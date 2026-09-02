<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==================== PUBLIC / AUTH ====================
$routes->get('/', 'DashboardController::index', ['filter' => 'auth']);
$routes->get('login', 'AuthController::loginForm');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
$routes->get('forgot-password', 'AuthController::forgotPasswordForm');
$routes->post('forgot-password', 'AuthController::forgotPassword');

// ==================== DASHBOARD ====================
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);

// ==================== MODUL 1: REPOSITORY DIGITAL (khusus BACA untuk user biasa) ====================
$routes->group('dokumen', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'RepositoryController::index');
    $routes->get('suggest', 'RepositoryController::suggest'); // AJAX autocomplete
    $routes->get('kategori/(:segment)', 'RepositoryController::kategori/$1');
    $routes->get('(:num)', 'RepositoryController::detail/$1');
    $routes->get('(:num)/preview', 'RepositoryController::preview/$1');
});
// Streaming gambar preview & cover (path terpisah, tidak menyingkap struktur folder writable)
$routes->get('preview-image/(:any)', 'RepositoryController::streamPreviewImage/$1', ['filter' => 'auth']);
$routes->get('cover-image/(:any)', 'RepositoryController::streamCoverImage/$1', ['filter' => 'auth']);

// ==================== MODUL 2: INNOVATION HUB ====================
// CATATAN: aplikasi ini BUKAN untuk pengajuan inovasi mandiri oleh karyawan.
// Konten inovasi sepenuhnya dikelola oleh Admin/Super Admin (lihat grup 'admin' di bawah).
// Karyawan hanya bisa melihat, memberi apresiasi (like), dan menyimpan (bookmark).
$routes->group('inovasi', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'InnovationController::index');
    $routes->get('bacaan-saya', 'InnovationController::bacaanSaya');
    $routes->get('(:num)', 'InnovationController::detail/$1');
    $routes->post('(:num)/like', 'InnovationController::like/$1');
    $routes->post('(:num)/bookmark', 'InnovationController::bookmark/$1');
});
$routes->get('inovasi-image/(:any)', 'InnovationController::streamCoverImage/$1', ['filter' => 'auth']);

// ==================== MODUL 3: ADMIN & CONTROL PANEL ====================
$routes->group('admin', ['filter' => ['auth', 'admin']], static function ($routes) {

    // ---- Management Berkas (Dokumen) - CRUD lengkap ----
    $routes->get('dokumen', 'Admin\DocumentManageController::index');
    $routes->get('dokumen/tambah', 'Admin\DocumentManageController::createForm');
    $routes->post('dokumen', 'Admin\DocumentManageController::store');
    $routes->get('dokumen/(:num)/edit', 'Admin\DocumentManageController::editForm/$1');
    $routes->post('dokumen/(:num)/update', 'Admin\DocumentManageController::update/$1');
    $routes->get('dokumen/(:num)/lihat-lengkap', 'Admin\DocumentManageController::viewFull/$1');
    $routes->get('dokumen/(:num)/download', 'Admin\DocumentManageController::download/$1');
    $routes->get('dokumen/(:num)/visibilitas', 'Admin\DocumentManageController::editVisibilitas/$1');
    $routes->post('dokumen/halaman/(:num)/hapus', 'Admin\DocumentManageController::hapusHalamanPreview/$1');
    $routes->post('dokumen/(:num)/hapus', 'Admin\DocumentManageController::destroy/$1');

    // ---- Management Kategori Dokumen - CRUD lengkap ----
    $routes->get('kategori', 'Admin\KategoriManageController::index');
    $routes->get('kategori/tambah', 'Admin\KategoriManageController::createForm');
    $routes->post('kategori', 'Admin\KategoriManageController::store');
    $routes->get('kategori/(:num)/edit', 'Admin\KategoriManageController::editForm/$1');
    $routes->post('kategori/(:num)/update', 'Admin\KategoriManageController::update/$1');
    $routes->post('kategori/(:num)/hapus', 'Admin\KategoriManageController::destroy/$1');

    // ---- Management Inovasi - CRUD lengkap (menggantikan pengajuan mandiri karyawan) ----
    $routes->get('inovasi', 'Admin\InnovationManageController::index');
    $routes->get('inovasi/tambah', 'Admin\InnovationManageController::createForm');
    $routes->post('inovasi', 'Admin\InnovationManageController::store');
    $routes->get('inovasi/(:num)/edit', 'Admin\InnovationManageController::editForm/$1');
    $routes->post('inovasi/(:num)/update', 'Admin\InnovationManageController::update/$1');
    $routes->post('inovasi/(:num)/hapus', 'Admin\InnovationManageController::destroy/$1');
    $routes->post('inovasi/lampiran/(:num)/hapus', 'Admin\InnovationManageController::hapusLampiran/$1');

    // ---- Management User & Akses - CRUD lengkap ----
    $routes->get('users', 'Admin\UserManageController::index');
    $routes->get('users/tambah', 'Admin\UserManageController::createForm');
    $routes->post('users', 'Admin\UserManageController::store');
    $routes->get('users/(:num)/edit', 'Admin\UserManageController::editForm/$1');
    $routes->post('users/(:num)/update', 'Admin\UserManageController::update/$1');
    $routes->post('users/(:num)/reset-password', 'Admin\UserManageController::resetPassword/$1');
    $routes->post('users/(:num)/toggle-status', 'Admin\UserManageController::toggleStatus/$1');

    // ---- Analytics Dashboard ----
    $routes->get('analytics', 'Admin\AnalyticsController::index');
    $routes->get('analytics/chart-data', 'Admin\AnalyticsController::chartData');
});
