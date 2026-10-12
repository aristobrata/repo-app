<?php
use CodeIgniter\Router\RouteCollection;
/** @var RouteCollection $routes */

// ==================== PUBLIC / AUTH ====================
$routes->get('/', 'DashboardController::index', ['filter' => 'auth']);
$routes->get('login', 'AuthController::loginForm');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
$routes->get('forgot-password', 'AuthController::forgotPasswordForm');
$routes->post('forgot-password', 'AuthController::forgotPassword');

// ==================== DASHBOARD ====================
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
$routes->get('dashboard/summary-inovasi', 'DashboardController::summaryInovasi', ['filter' => 'auth']);

// ==================== MODUL 1: LIBRARY (gabungan Inovasi + Knowledge Management) ====================
$routes->group('library', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'LibraryController::index');
    $routes->get('km/(:num)', 'LibraryController::kmDetail/$1');
    $routes->get('km/(:num)/download', 'LibraryController::downloadKmFile/$1');
});
$routes->get('library-image/(:any)', 'LibraryController::streamCoverImage/$1', ['filter' => 'auth']);

// ==================== MODUL 2: INOVASI ====================
$routes->group('inovasi', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'InnovationController::index');
    $routes->get('(:num)', 'InnovationController::detail/$1');
    $routes->post('(:num)/like', 'InnovationController::like/$1');
    $routes->get('(:num)/download', 'InnovationController::downloadDokumen/$1');
});
$routes->get('inovasi-image/(:any)', 'InnovationController::streamCoverImage/$1', ['filter' => 'auth']);

// ==================== MODUL 3: ADMIN & CONTROL PANEL ====================
$routes->group('admin', ['filter' => ['auth', 'admin']], static function ($routes) {

    // Management Inovasi (CRUD + Import Excel)
    $routes->get('inovasi', 'Admin\InovasiManageController::index');
    $routes->get('inovasi/tambah', 'Admin\InovasiManageController::createForm');
    $routes->post('inovasi', 'Admin\InovasiManageController::store');
    $routes->get('inovasi/import', 'Admin\InovasiManageController::importForm');
    $routes->post('inovasi/import', 'Admin\InovasiManageController::import');
    $routes->get('inovasi/(:num)/edit', 'Admin\InovasiManageController::editForm/$1');
    $routes->post('inovasi/(:num)/update', 'Admin\InovasiManageController::update/$1');
    $routes->post('inovasi/(:num)/hapus', 'Admin\InovasiManageController::destroy/$1');
    $routes->get('inovasi/export', 'Admin\InovasiManageController::exportExcel');

    // Management Knowledge Management (CRUD + Import Excel, 3 sheet)
    $routes->get('knowledge', 'Admin\KmManageController::index');
    $routes->get('knowledge/tambah', 'Admin\KmManageController::createForm');
    $routes->post('knowledge', 'Admin\KmManageController::store');
    $routes->post('knowledge/(:num)/hapus', 'Admin\KmManageController::destroy/$1');
    $routes->get('knowledge/karyawan', 'Admin\KmManageController::karyawan');
    $routes->get('knowledge/rekap', 'Admin\KmManageController::rekap');
    $routes->get('knowledge/target', 'Admin\KmManageController::target');
    $routes->post('knowledge/target', 'Admin\KmManageController::storeTarget');
    $routes->get('knowledge/import', 'Admin\KmManageController::importForm');
    $routes->post('knowledge/import/aktivitas', 'Admin\KmManageController::importAktivitas');
    $routes->post('knowledge/import/karyawan', 'Admin\KmManageController::importKaryawan');
    $routes->post('knowledge/import/rekap', 'Admin\KmManageController::importRekap');
    $routes->get('knowledge/export', 'Admin\KmManageController::exportAktivitas');
    $routes->get('knowledge/karyawan/export', 'Admin\KmManageController::exportKaryawan');
    $routes->get('knowledge/rekap/export', 'Admin\KmManageController::exportRekap');

    // Management User & Akses
    $routes->get('users', 'Admin\UserManageController::index');
    $routes->get('users/tambah', 'Admin\UserManageController::createForm');
    $routes->post('users', 'Admin\UserManageController::store');
    $routes->get('users/(:num)/edit', 'Admin\UserManageController::editForm/$1');
    $routes->post('users/(:num)/update', 'Admin\UserManageController::update/$1');
    $routes->post('users/(:num)/reset-password', 'Admin\UserManageController::resetPassword/$1');
    $routes->post('users/(:num)/toggle-status', 'Admin\UserManageController::toggleStatus/$1');

    // Analytics Dashboard
    $routes->get('analytics', 'Admin\AnalyticsController::index');
    $routes->get('analytics/chart-data', 'Admin\AnalyticsController::chartData');
});
