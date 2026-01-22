<?php
// Ambil nama folder aktif untuk highlight menu
$current_page = basename(dirname($_SERVER['PHP_SELF']));
?>

<style>
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

	.sidebar .brand i {
		margin-right: 10px;
	}

	.sidebar .nav-link {
		color: rgba(255, 255, 255, 0.8);
		padding: 12px 25px;
		margin: 5px 15px;
		border-radius: 8px;
		transition: all 0.3s ease;
		display: flex;
		align-items: center;
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
		margin-bottom: 5px;
	}

	.sidebar .user-info .role {
		color: rgba(255, 255, 255, 0.7);
		font-size: 0.85rem;
	}

	.sidebar .btn-logout {
		width: 100%;
		margin-top: 10px;
	}

	.main-content {
		margin-left: 250px;
		min-height: 100vh;
		background: #f6f9fc;
	}

	.top-navbar {
		background: #fff;
		padding: 15px 25px;
		box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.top-navbar .page-title {
		font-size: 1.25rem;
		font-weight: 600;
		color: #333;
		margin: 0;
	}

	.content-wrapper {
		padding: 25px;
	}

	.card {
		border: none;
		border-radius: 10px;
	}

	@media (max-width: 768px) {
		.sidebar {
			width: 70px;
			overflow: hidden;
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

		.sidebar .nav-link i {
			margin: 0;
			font-size: 1.3rem;
		}

		.main-content {
			margin-left: 70px;
		}
	}
</style>

<div class="sidebar">
	<div class="brand">
		<i class="bi bi-mortarboard-fill"></i>
		<span>SIA Panel</span>
	</div>

	<nav class="nav flex-column">
		<a class="nav-link <?= ($current_page == 'workshop_aslab' || $current_page == 'index.php') ? 'active' : '' ?>" href="/index.php">
			<i class="bi bi-speedometer2"></i>
			<span>Dashboard</span>
		</a>
		<a class="nav-link <?= $current_page == 'mahasiswa' ? 'active' : '' ?>" href="/admin/mahasiswa/index.php">
			<i class="bi bi-people-fill"></i>
			<span>Data Mahasiswa</span>
		</a>
		<a class="nav-link <?= $current_page == 'dosen' ? 'active' : '' ?>" href="/admin/dosen/index.php">
			<i class="bi bi-person-badge-fill"></i>
			<span>Data Dosen</span>
		</a>
		<a class="nav-link <?= $current_page == 'matkul' ? 'active' : '' ?>" href="/admin/matkul/index.php">
			<i class="bi bi-book-fill"></i>
			<span>Data Matkul</span>
		</a>
		<a class="nav-link <?= $current_page == 'nilai' ? 'active' : '' ?>" href="/admin/nilai/index.php">
			<i class="bi bi-journal-check"></i>
			<span>Data Nilai</span>
		</a>
		<a class="nav-link <?= $current_page == 'users' ? 'active' : '' ?>" href="/admin/users/index.php">
			<i class="bi bi-shield-lock-fill"></i>
			<span>Manajemen User</span>
		</a>
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
		<a href="/auth/logout.php" class="btn btn-danger btn-sm btn-logout">
			<i class="bi bi-box-arrow-right me-1"></i>Logout
		</a>
	</div>
</div>
