<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/User.php';

// Inisialisasi koneksi database dan objek User
$db = new Database();
$userObj = new User($db);

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
	header("location: ../../auth/login.php");
	exit();
}

// Cek apakah user memiliki akses level admin
if ($_SESSION['level'] != 'admin') {
	header("location: ../../index.php");
	exit();
}

$pesan = '';
$tipe_pesan = '';

// Proses tambah data user
if (isset($_POST['tambah'])) {
	$result = $userObj->tambah($_POST['username'], $_POST['password'], $_POST['level']);

	if ($result === true) {
		$pesan = "User berhasil ditambahkan!";
		$tipe_pesan = "success";
	} else {
		$pesan = $result;
		$tipe_pesan = "danger";
	}
}

// Proses hapus data user
if (isset($_GET['hapus'])) {
	$result = $userObj->hapus($_GET['hapus'], $_SESSION['username']);

	if ($result === true) {
		$pesan = "User berhasil dihapus!";
		$tipe_pesan = "success";
	} elseif ($result == "Tidak dapat menghapus akun sendiri!") {
		$pesan = $result;
		$tipe_pesan = "warning";
	} else {
		$pesan = $result;
		$tipe_pesan = "danger";
	}
}

// Proses update data user
if (isset($_POST['update'])) {
	$result = $userObj->update($_POST['id'], $_POST['username'], $_POST['password'], $_POST['level']);

	if ($result === true) {
		$pesan = "User berhasil diupdate!";
		$tipe_pesan = "success";
	} else {
		$pesan = $result;
		$tipe_pesan = "danger";
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Manajemen User | SIA</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
	<style>
		.badge-admin {
			background: linear-gradient(135deg, #dc3545, #c82333);
		}

		.badge-dosen {
			background: linear-gradient(135deg, #28a745, #218838);
		}

		.badge-mahasiswa {
			background: linear-gradient(135deg, #007bff, #0056b3);
		}
	</style>
</head>

<body>
	<?php include '../../includes/sidebar.php'; ?>

	<div class="main-content">
		<div class="top-navbar">
			<h5 class="page-title">
				<i class="bi bi-shield-lock-fill text-primary me-2"></i>Manajemen User
			</h5>
			<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
				<i class="bi bi-plus-circle me-1"></i> Tambah User
			</button>
		</div>

		<div class="content-wrapper">
			<!-- Alert Pesan -->
			<?php if ($pesan != ''): ?>
				<div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
					<?= $pesan ?>
					<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				</div>
			<?php endif; ?>

			<!-- Card Tabel -->
			<div class="card shadow-sm">
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover">
							<thead class="table-primary">
								<tr class="text-center">
									<th width="5%">No</th>
									<th width="30%">Username</th>
									<th width="20%">Level</th>
									<th width="35%">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$no = 1;
								$data = $userObj->tampil();

								if (mysqli_num_rows($data) > 0) {
									while ($row = mysqli_fetch_assoc($data)) {
										// Badge berdasarkan level
										$badge_class = '';
										switch ($row['level']) {
											case 'admin':
												$badge_class = 'badge-admin';
												break;
											case 'dosen':
												$badge_class = 'badge-dosen';
												break;
											case 'mahasiswa':
												$badge_class = 'badge-mahasiswa';
												break;
										}

										echo "<tr>
											<td class='text-center'>" . $no++ . "</td>
											<td>" . htmlspecialchars($row['username']) . "</td>
											<td class='text-center'>
												<span class='badge $badge_class'>" . ucfirst(htmlspecialchars($row['level'])) . "</span>
											</td>
											<td class='text-center'>
												<button class='btn btn-warning btn-sm me-1' data-bs-toggle='modal' data-bs-target='#modalEdit" . $row['id'] . "'>
													<i class='bi bi-pencil-square'></i> Edit
												</button>
												<a href='index.php?hapus=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus user " . htmlspecialchars($row['username']) . "?')\">
													<i class='bi bi-trash'></i> Hapus
												</a>
											</td>
										</tr>";

										// Modal Edit
										echo '
										<div class="modal fade" id="modalEdit' . $row['id'] . '" tabindex="-1">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-header bg-warning">
														<h5 class="modal-title">
															<i class="bi bi-pencil-square me-2"></i>Edit User
														</h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
													</div>

													<form method="POST" action="index.php">
														<div class="modal-body">
															<input type="hidden" name="id" value="' . $row['id'] . '">

															<div class="mb-3">
																<label class="form-label">Username</label>
																<input type="text" name="username" class="form-control" value="' . htmlspecialchars($row['username']) . '" required>
															</div>

															<div class="mb-3">
																<label class="form-label">Password Baru</label>
																<input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
																<small class="text-muted">Biarkan kosong jika tidak ingin mengubah password</small>
															</div>

															<div class="mb-3">
																<label class="form-label">Level</label>
																<select name="level" class="form-select" required>
																	<option value="admin" ' . ($row['level'] == 'admin' ? 'selected' : '') . '>Admin</option>
																	<option value="dosen" ' . ($row['level'] == 'dosen' ? 'selected' : '') . '>Dosen</option>
																	<option value="mahasiswa" ' . ($row['level'] == 'mahasiswa' ? 'selected' : '') . '>Mahasiswa</option>
																</select>
															</div>
														</div>

														<div class="modal-footer">
															<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
																<i class="bi bi-x-circle me-1"></i>Batal
															</button>
															<button type="submit" name="update" class="btn btn-primary">
																<i class="bi bi-check-circle me-1"></i>Simpan Perubahan
															</button>
														</div>
													</form>
												</div>
											</div>
										</div>';
									}
								} else {
									echo '<tr><td colspan="5" class="text-center text-muted">Belum ada data user</td></tr>';
								}
								?>
							</tbody>
						</table>
					</div>

					<!-- Info jumlah data -->
					<?php
					$total = $userObj->jumlahData();
					$total_admin = $userObj->jumlahByLevel('admin');
					$total_dosen = $userObj->jumlahByLevel('dosen');
					$total_mahasiswa = $userObj->jumlahByLevel('mahasiswa');
					?>
					<div class="text-muted small">
						<i class="bi bi-info-circle me-1"></i>
						Total: <?= $total ?> user
						(<span class="text-danger"><?= $total_admin ?> Admin</span>,
						<span class="text-success"><?= $total_dosen ?> Dosen</span>,
						<span class="text-primary"><?= $total_mahasiswa ?> Mahasiswa</span>)
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Tambah -->
	<div class="modal fade" id="modalTambah" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-success text-white">
					<h5 class="modal-title">
						<i class="bi bi-plus-circle me-2"></i>Tambah User
					</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
				</div>

				<form method="POST" action="index.php">
					<div class="modal-body">
						<div class="mb-3">
							<label class="form-label">Username <span class="text-danger">*</span></label>
							<input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
						</div>

						<div class="mb-3">
							<label class="form-label">Password <span class="text-danger">*</span></label>
							<input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
						</div>

						<div class="mb-3">
							<label class="form-label">Level <span class="text-danger">*</span></label>
							<select name="level" class="form-select" required>
								<option value="">-- Pilih Level --</option>
								<option value="admin">Admin</option>
								<option value="dosen">Dosen</option>
								<option value="mahasiswa">Mahasiswa</option>
							</select>
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
							<i class="bi bi-x-circle me-1"></i>Batal
						</button>
						<button type="submit" name="tambah" class="btn btn-success">
							<i class="bi bi-check-circle me-1"></i>Simpan
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>