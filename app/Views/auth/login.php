<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Internal Knowledge Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Palet warna diambil dari logo PT Semen Padang (merah #D73338 & hitam/charcoal #272727) */
        body { background: #F8FAFC; }
        .login-topbar { height: 6px; background: linear-gradient(90deg, #272727, #D73338); }
        .login-logo { height: 72px; width: auto; }
        .card { border-radius: 10px; border-top: 3px solid #D73338; }
        .card-title { color: #272727; }
        .btn-primary { background-color: #D73338; border-color: #D73338; }
        .btn-primary:hover { background-color: #A8262A; border-color: #A8262A; }
        a { color: #D73338; }
        a:hover { color: #A8262A; }
    </style>
</head>
<body>
<div class="login-topbar"></div>
<div class="container d-flex align-items-center justify-content-center" style="min-height:calc(100vh - 6px);">
    <div class="card shadow-sm" style="width: 380px;">
        <div class="card-body p-4">
            <div class="text-center mb-3">
                <img src="<?= base_url('assets/img/logo-semen-padang.png') ?>" alt="Semen Padang" class="login-logo mb-2">
                <h5 class="card-title mb-0">Internal Digital Repository</h5>
                <p class="text-muted small mb-0">Knowledge Management &amp; Inovasi</p>
            </div>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger py-2"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <form action="<?= base_url('login') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="<?= old('email') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <div class="text-center mt-3"><a href="<?= base_url('forgot-password') ?>" class="small">Lupa password?</a></div>
            <p class="text-muted small text-center mt-3 mb-0">Akun karyawan dibuat oleh Admin.</p>
        </div>
    </div>
</div>
</body>
</html>
