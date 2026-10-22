<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Dashboard Knowledge Management & Inovasi' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Palet warna diambil dari logo PT Semen Padang (merah #D73338 & hitam/charcoal #272727) */
        :root {
            --sig-red: #D73338;
            --sig-red-dark: #A8262A;
            --sig-ink: #272727;
            --sig-gold: #D97706;
            --sig-bg: #F8FAFC;
            --sig-border: #E2E8F0;
            --sig-success: #10B981;
            --sig-muted: #64748B;
        }
        body { font-family: 'Inter', sans-serif; background: var(--sig-bg); color: #0F2027; }

        /* Navbar bernuansa Semen Padang: dasar hitam/charcoal + garis aksen merah di bawah */
        .navbar { background: var(--sig-ink) !important; border-bottom: 3px solid var(--sig-red); }
        .navbar-brand { font-weight: 700; }
        .navbar-nav .nav-link.active, .navbar-nav .nav-link:hover { color: #fff !important; }

        .card { border-radius: 10px; border: 1px solid var(--sig-border); box-shadow: none; }

        /* Timpa warna "primary" Bootstrap bawaan (biru) jadi merah brand di seluruh halaman */
        .btn-primary { background-color: var(--sig-red); border-color: var(--sig-red); }
        .btn-primary:hover, .btn-primary:focus { background-color: var(--sig-red-dark); border-color: var(--sig-red-dark); }
        .btn-outline-primary { color: var(--sig-red); border-color: var(--sig-red); }
        .btn-outline-primary:hover { background-color: var(--sig-red); border-color: var(--sig-red); }
        .bg-primary { background-color: var(--sig-red) !important; }
        .text-primary { color: var(--sig-red) !important; }
        .border-primary { border-color: var(--sig-red) !important; }
        a { color: var(--sig-red); }
        a:hover { color: var(--sig-red-dark); }

        .text-sig-gold { color: var(--sig-gold); }

        /* Garis aksen merah tipis di atas setiap card, konsisten dengan identitas Semen Padang */
        .card:not(.border-0) { border-top: 3px solid var(--sig-red); }

        .pagination .page-item.active .page-link { background-color: var(--sig-red); border-color: var(--sig-red); color: #fff; }
        .pagination .page-link { color: var(--sig-red); }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= base_url('dashboard') ?>">
            <img src="<?= base_url('assets/img/logo-semen-padang.png') ?>" alt="Semen Padang" style="height:32px; width:auto;">
            <span>KM & Inovasi Center</span>
        </a>
        <?php if (session()->get('logged_in')): ?>
        <div class="navbar-nav ms-auto d-flex flex-row gap-3">
            <a class="nav-link text-light" href="<?= base_url('dashboard') ?>">Dashboard</a>
            <a class="nav-link text-light" href="<?= base_url('library') ?>">Library</a>
            <a class="nav-link text-light" href="<?= base_url('inovasi') ?>">Inovasi &amp; KM</a>
            <?php if (in_array(session()->get('role'), ['admin', 'super_admin'])): ?>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-warning" href="#" role="button" data-bs-toggle="dropdown">Admin Panel</a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= base_url('admin/inovasi') ?>">Kelola Inovasi</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/inovasi/import') ?>">Import Excel Inovasi</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/inovasi-karyawan') ?>">Master Karyawan Inovasi</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/kompetisi') ?>">Kompetisi Inovasi</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/knowledge') ?>">Kelola Aktivitas KM</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/knowledge/karyawan') ?>">Master Karyawan</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/knowledge/rekap') ?>">Rekap/Leaderboard</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/knowledge/target') ?>">Target Tahunan</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/knowledge/import') ?>">Import Excel KM</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/users') ?>">Kelola User</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/analytics') ?>">Analytics</a></li>
                    </ul>
                </div>
            <?php endif; ?>
            <a class="nav-link text-light" href="<?= base_url('logout') ?>">Logout (<?= esc(session()->get('nama')) ?>)</a>
        </div>
        <?php endif; ?>
    </div>
</nav>
<div class="container-fluid px-4 my-4">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?= $this->renderSection('content') ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
