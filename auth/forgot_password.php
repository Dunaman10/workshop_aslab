<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/User.php';

// Inisialisasi Objek
$db = new Database();
$userObj = new User($db);

$pesan = '';
$tipe_pesan = '';
$step = 1; // Step 1: Input Username, Step 2: Input Password Baru
$username_valid = '';

// Proses Cek Username (Step 1)
if (isset($_POST['cek_username'])) {
    $username = $_POST['username'];
    $user = $userObj->findByUsername($username);

    if ($user) {
        $step = 2;
        $username_valid = $user['username'];
        $pesan = "Username ditemukan! Silakan masukkan password baru.";
        $tipe_pesan = "success";
    } else {
        $pesan = "Username tidak ditemukan.";
        $tipe_pesan = "danger";
    }
}

// Proses Reset Password (Step 2)
if (isset($_POST['reset_password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi_password'];

    if ($password == $konfirmasi) {
        if ($userObj->resetPasswordByUsername($username, $password)) {
            $pesan = "Password berhasil diubah! Silakan login dengan password baru.";
            $tipe_pesan = "success";
            $step = 3; // Selesai
        } else {
            $pesan = "Gagal mengubah password.";
            $tipe_pesan = "danger";
            $step = 2;
            $username_valid = $username;
        }
    } else {
        $pesan = "Konfirmasi password tidak cocok!";
        $tipe_pesan = "danger";
        $step = 2;
        $username_valid = $username;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | SIA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #3B82F6, #60A5FA);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            border: none;
            width: 400px;
        }

        .card-header {
            background: #fff;
            border-radius: 15px 15px 0 0 !important;
            padding: 25px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-header">
            <h4 class="mb-1 text-primary">Reset Password</h4>
            <small class="text-muted">Metode Reset via Username</small>
        </div>

        <div class="card-body p-4">
            <?php if ($pesan != ''): ?>
                <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show">
                    <?= $pesan ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($step == 1): ?>
                <!-- STEP 1: Input Username -->
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label">Masukkan Username Anda</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="cek_username" class="btn btn-primary">
                            <i class="bi bi-search me-2"></i>Cari Akun
                        </button>
                        <a href="login.php" class="btn btn-light text-muted">
                            <i class="bi bi-arrow-left me-2"></i>Kembali ke Login
                        </a>
                    </div>
                </form>

            <?php elseif ($step == 2): ?>
                <!-- STEP 2: Input Password Baru -->
                <form method="POST">
                    <input type="hidden" name="username" value="<?= htmlspecialchars($username_valid) ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6" autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Ulangi Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ulangi password baru" required minlength="6">
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="reset_password" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i>Simpan Password
                        </button>
                        <a href="forgot_password.php" class="btn btn-light text-muted">Batal</a>
                    </div>
                </form>
            
            <?php elseif ($step == 3): ?>
                <!-- STEP 3: Sukses -->
                <div class="text-center mt-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    <p class="mt-3">Password Anda berhasil diperbarui.</p>
                    <a href="login.php" class="btn btn-primary w-100">Login Sekarang</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
