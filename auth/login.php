<?php
session_start();
include '../config/koneksi.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['username'])) {
	header("location: ../index.php");
	exit();
}

$alert_gagal_login = '';

if (isset($_POST['login'])) {
	$username = mysqli_real_escape_string($koneksi, $_POST['username']);
	$password = $_POST['password'];

	$cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
	$data = mysqli_fetch_assoc($cek);

	if ($data && password_verify($password, $data['password'])) {
		// Login berhasil
		$_SESSION['username'] = $data['username'];
		$_SESSION['level'] = $data['level'];
		$_SESSION['id'] = $data['id'];

		// Redirect berdasarkan level
		if ($data['level'] == "admin") {
			header("location: ../admin/index.php");
			exit();
		} else {
			// Dosen dan Mahasiswa ke dashboard umum
			header("location: ../index.php");
			exit();
		}
	} else {
		$alert_gagal_login = "Username atau password salah.";
	}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Halaman Login | Sistem Informasi Akademik</title>

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
			border-radius: 20px;
			box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
			border: none;
		}

		.card-header {
			background: linear-gradient(135deg, #0d6efd, #0b5ed7);
			border-radius: 20px 20px 0 0 !important;
			padding: 25px;
		}

		.form-control:focus {
			border-color: #0d6efd;
			box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
		}

		.btn-login {
			padding: 12px;
			font-weight: 600;
		}
	</style>

</head>

<body>
	<div class="card" style="width: 380px">
		<div class="card-header text-center text-white">
			<i class="bi bi-mortarboard-fill" style="font-size: 3rem;"></i>
			<h4 class="mt-2 mb-0">Sistem Informasi Akademik</h4>
			<small>Silakan login untuk melanjutkan</small>
		</div>

		<div class="card-body p-4">
			<?php if ($alert_gagal_login != ''): ?>
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<i class="bi bi-exclamation-triangle-fill me-2"></i>
					<?= $alert_gagal_login ?>
					<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				</div>
			<?php endif; ?>

			<form action="" method="post">
				<div class="mb-3">
					<label class="form-label">
						<i class="bi bi-person-fill me-1"></i>Username
					</label>
					<input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
				</div>
				<div class="mb-4">
					<label class="form-label">
						<i class="bi bi-lock-fill me-1"></i>Password
					</label>
					<input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
				</div>
				<div class="mb-4 text-end">
					<a href="forgot_password.php" class="text-decoration-none small text-muted">Lupa Password?</a>
				</div>
				<button type="submit" name="login" class="btn btn-primary btn-login w-100">
					<i class="bi bi-box-arrow-in-right me-2"></i>Login
				</button>
			</form>
		</div>

		<div class="card-footer text-center text-muted bg-light" style="border-radius: 0 0 20px 20px;">
			<small>&copy; 2026 SIA - Workshop Aslab</small>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>