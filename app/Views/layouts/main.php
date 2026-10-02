<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Dashboard Knowledge Management & Inovasi' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --sig-red: #C8102E;
            --sig-dark-blue: #1E3A8A;
            --sig-gold: #D97706;
            --sig-bg: #F8FAFC;
            --sig-border: #E2E8F0;
            --sig-success: #10B981;
            --sig-muted: #64748B;
        }
        body { font-family: 'Inter', sans-serif; background: var(--sig-bg); color: #0F2027; }
        .navbar { background: var(--sig-dark-blue) !important; }
        .navbar-brand { font-weight: 700; }
        .card { border-radius: 8px; border: 1px solid var(--sig-border); box-shadow: none; }
        .btn-primary { background-color: var(--sig-red); border-color: var(--sig-red); }
        .btn-primary:hover { background-color: #a30d25; border-color: #a30d25; }
        .text-sig-gold { color: var(--sig-gold); }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="<?= base_url('dashboard') ?>">📊 KM & Inovasi Center</a>
        <?php if (session()->get('logged_in')): ?>
        <div class="navbar-nav ms-auto d-flex flex-row gap-3">
            <a class="nav-link text-light" href="<?= base_url('dashboard') ?>">Dashboard</a>
            <a class="nav-link text-light" href="<?= base_url('dokumen') ?>">Repository</a>
            <a class="nav-link text-light" href="<?= base_url('inovasi') ?>">Inovasi</a>
            <?php if (in_array(session()->get('role'), ['admin', 'super_admin'])): ?>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-warning" href="#" role="button" data-bs-toggle="dropdown">Admin Panel</a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= base_url('admin/dokumen') ?>">Kelola Dokumen</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/kategori') ?>">Kelola Kategori</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/inovasi') ?>">Kelola Inovasi</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/inovasi/import') ?>">Import Excel Inovasi</a></li>
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
