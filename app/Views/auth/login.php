<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Internal Knowledge Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="card shadow-sm" style="width: 380px;">
        <div class="card-body p-4">
            <h5 class="card-title mb-3 text-center">Internal Digital Repository</h5>

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
            <div class="text-center mt-3">
                <a href="<?= base_url('forgot-password') ?>" class="small">Lupa password?</a>
            </div>
            <p class="text-muted small text-center mt-3 mb-0">
                Akun karyawan dibuat oleh Admin. Hubungi Admin jika belum punya akses.
            </p>
        </div>
    </div>
</div>
</body>
</html>
