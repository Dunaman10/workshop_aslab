<?php
session_start();
include 'config/koneksi.php';

// Cek Login
if (!isset($_SESSION['username'])) {
	header("location: auth/login.php");
	exit();
}

// Ambil nama folder aktif untuk highlight menu
$current_page = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Dashboard | SIA</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
	<style>
		body {
			background: #f6f9fc;
		}

		.sidebar {
			position: fixed;
			top: 0;
			left: 0;
			width: 250px;
			height: 100vh;
			background: linear-gradient(180deg, #0d6efd 0%, #0b5ed7 100%);
			padding-top: 20px;
			z-index: 1000;
			box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
		}

		.sidebar .brand {
			color: #fff;
			font-size: 1.5rem;
			font-weight: bold;
			text-align: center;
			padding: 15px 20px;
			border-bottom: 1px solid rgba(255, 255, 255, 0.2);
			margin-bottom: 20px;
		}

		.sidebar .nav-link {
			color: rgba(255, 255, 255, 0.8);
			padding: 12px 25px;
			margin: 5px 15px;
			border-radius: 8px;
			transition: all 0.3s ease;
		}

		.sidebar .nav-link:hover {
			color: #fff;
			background: rgba(255, 255, 255, 0.15);
			transform: translateX(5px);
		}

		.sidebar .nav-link.active {
			color: #0d6efd;
			background: #fff;
			font-weight: 600;
		}

		.sidebar .nav-link i {
			margin-right: 12px;
			font-size: 1.1rem;
			width: 20px;
			text-align: center;
		}

		.sidebar .user-info {
			position: absolute;
			bottom: 0;
			left: 0;
			right: 0;
			padding: 20px;
			border-top: 1px solid rgba(255, 255, 255, 0.2);
			background: rgba(0, 0, 0, 0.1);
		}

		.sidebar .user-info .username {
			color: #fff;
			font-weight: 500;
		}

		.sidebar .user-info .role {
			color: rgba(255, 255, 255, 0.7);
			font-size: 0.85rem;
		}

		.main-content {
			margin-left: 250px;
			min-height: 100vh;
		}

		.top-navbar {
			background: #fff;
			padding: 20px 25px;
			box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
		}

		.content-wrapper {
			padding: 25px;
		}

		.welcome-banner {
			background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
			border-radius: 15px;
			padding: 30px;
			color: #fff;
		}

		@media (max-width: 768px) {
			.sidebar {
				width: 70px;
			}

			.sidebar .brand span,
			.sidebar .nav-link span,
			.sidebar .user-info {
				display: none;
			}

			.sidebar .nav-link {
				justify-content: center;
				padding: 15px;
				margin: 5px;
			}

			.main-content {
				margin-left: 70px;
			}
		}
	</style>
</head>

<body>
	<!-- Sidebar -->
	<div class="sidebar">
		<div class="brand">
			<i class="bi bi-mortarboard-fill"></i>
			<span>SIA Panel</span>
		</div>

		<nav class="nav flex-column">
			<a class="nav-link active" href="/index.php">
				<i class="bi bi-speedometer2"></i>
				<span>Dashboard</span>
			</a>
			<?php if ($_SESSION['level'] == 'admin'): ?>
				<a class="nav-link" href="/admin/mahasiswa/index.php">
					<i class="bi bi-people-fill"></i>
					<span>Data Mahasiswa</span>
				</a>
				<a class="nav-link" href="/admin/dosen/index.php">
					<i class="bi bi-person-badge-fill"></i>
					<span>Data Dosen</span>
				</a>
				<a class="nav-link" href="/admin/matkul/index.php">
					<i class="bi bi-book-fill"></i>
					<span>Data Matkul</span>
				</a>
				<a class="nav-link" href="/admin/nilai/index.php">
					<i class="bi bi-journal-check"></i>
					<span>Data Nilai</span>
				</a>
				<a class="nav-link" href="/admin/users/index.php">
					<i class="bi bi-shield-lock-fill"></i>
					<span>Manajemen User</span>
				</a>
			<?php endif; ?>
		</nav>

		<div class="user-info">
			<div class="username">
				<i class="bi bi-person-circle me-1"></i>
				<?= htmlspecialchars($_SESSION['username']); ?>
			</div>
			<div class="role">
				<i class="bi bi-shield-check me-1"></i>
				<?= ucfirst(htmlspecialchars($_SESSION['level'])); ?>
			</div>
			<a href="/auth/logout.php" class="btn btn-danger btn-sm w-100 mt-2">
				<i class="bi bi-box-arrow-right me-1"></i>Logout
			</a>
		</div>
	</div>

	<!-- Main Content -->
	<div class="main-content">
		<div class="top-navbar">
			<h5 class="mb-0">
				<i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard
			</h5>
		</div>

		<div class="content-wrapper">
			<!-- Welcome Banner -->
			<div class="welcome-banner">
				<h3>Selamat Datang, <?= htmlspecialchars($_SESSION['username']); ?>! 👋</h3>
				<p class="mb-0 opacity-75">
					Anda login sebagai <strong><?= ucfirst(htmlspecialchars($_SESSION['level'])); ?></strong>.
				</p>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
