<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Internal Digital Repository & Knowledge Center' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= $this->renderSection('styles') ?>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('dashboard') ?>">Knowledge Center</a>
        <?php if (session()->get('logged_in')): ?>
        <div class="navbar-nav ms-auto d-flex flex-row gap-3">
            <a class="nav-link text-light" href="<?= base_url('dokumen') ?>">Repository</a>
            <a class="nav-link text-light" href="<?= base_url('inovasi') ?>">Inovasi & Knowledge</a>
            <?php if (in_array(session()->get('role'), ['admin', 'super_admin'])): ?>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-warning" href="#" role="button" data-bs-toggle="dropdown">Admin Panel</a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= base_url('admin/dokumen') ?>">Kelola Dokumen</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/kategori') ?>">Kelola Kategori</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/inovasi') ?>">Kelola Inovasi</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/knowledge') ?>">Kelola Knowledge Hub</a></li>
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
<div class="container my-4">
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
