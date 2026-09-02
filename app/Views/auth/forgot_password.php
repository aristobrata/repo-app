<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="card shadow-sm" style="width: 380px;">
        <div class="card-body p-4">
            <h5 class="card-title mb-3">Reset Password</h5>
            <form action="<?= base_url('forgot-password') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Kirim Instruksi Reset</button>
            </form>
            <div class="text-center mt-3">
                <a href="<?= base_url('login') ?>" class="small">Kembali ke Login</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
