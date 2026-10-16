<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem POS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f4f8; /* Soft blue background */
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card login-card p-4">
                    <h3 class="text-center mb-4 fw-bold" style="color: #2c3e50;">Masuk Sistem</h3>
                    
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger p-2 text-center">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('auth/process') ?>" method="post">
                        <!-- CSRF Protection -->
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control form-control-lg bg-light" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg bg-light" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg rounded-3 fw-bold" style="background-color: #4a90e2; border: none;">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>