<?php
session_start();
require_once '../../classes/Database.php';
require_once '../../classes/Mahasiswa.php';

// Inisialisasi koneksi database dan objek Mahasiswa
$db = new Database();
$mhs = new Mahasiswa($db);

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

// Proses tambah data mahasiswa
if (isset($_POST['tambah'])) {
	$result = $mhs->tambah($_POST['nim'], $_POST['nama'], $_POST['prodi'], $_POST['angkatan']);
	
	if ($result === true) {
		$pesan = "Data mahasiswa berhasil ditambahkan!";
		$tipe_pesan = "success";
	} else {
		$pesan = $result;
		$tipe_pesan = "danger";
	}
}

// Proses hapus data mahasiswa
if (isset($_GET['hapus'])) {
	$result = $mhs->hapus($_GET['hapus']);
	
	if ($result === true) {
		$pesan = "Data mahasiswa berhasil dihapus!";
		$tipe_pesan = "success";
	} else {
		$pesan = "Gagal menghapus data!";
		$tipe_pesan = "danger";
	}
}

// Proses update data mahasiswa
if (isset($_POST['update'])) {
	$result = $mhs->update($_POST['nim_lama'], $_POST['nim'], $_POST['nama'], $_POST['prodi'], $_POST['angkatan']);
	
	if ($result === true) {
		$pesan = "Data mahasiswa berhasil diupdate!";
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
	<title>Data Mahasiswa | SIA</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body>
	<?php include '../../includes/sidebar.php'; ?>

	<div class="main-content">
		<div class="top-navbar">
			<h5 class="page-title">
				<i class="bi bi-people-fill text-primary me-2"></i>Data Mahasiswa
			</h5>
			<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
				<i class="bi bi-plus-circle me-1"></i> Tambah Mahasiswa
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
									<th width="15%">NIM</th>
									<th width="25%">Nama</th>
									<th width="25%">Program Studi</th>
									<th width="10%">Angkatan</th>
									<th width="20%">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$no = 1;
								$data = $mhs->tampil();

								if (mysqli_num_rows($data) > 0) {
									while ($row = mysqli_fetch_assoc($data)) {
										echo "<tr>
											<td class='text-center'>" . $no++ . "</td>
											<td>" . htmlspecialchars($row['nim']) . "</td>
											<td>" . htmlspecialchars($row['nama']) . "</td>
											<td>" . htmlspecialchars($row['prodi']) . "</td>
											<td class='text-center'><span class='badge bg-primary'>" . htmlspecialchars($row['angkatan']) . "</span></td>
											<td class='text-center'>
												<button class='btn btn-warning btn-sm me-1' data-bs-toggle='modal' data-bs-target='#modalEdit" . $row['nim'] . "'>
													<i class='bi bi-pencil-square'></i> Edit
												</button>
												<a href='index.php?hapus=" . $row['nim'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus data " . htmlspecialchars($row['nama']) . "?')\">
													<i class='bi bi-trash'></i> Hapus
												</a>
											</td>
										</tr>";

										// Modal Edit
										echo '
										<div class="modal fade" id="modalEdit' . $row['nim'] . '" tabindex="-1">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-header bg-warning">
														<h5 class="modal-title">
															<i class="bi bi-pencil-square me-2"></i>Edit Data Mahasiswa
														</h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
													</div>

													<form method="POST" action="index.php">
														<div class="modal-body">
															<input type="hidden" name="nim_lama" value="' . $row['nim'] . '">

															<div class="mb-3">
																<label class="form-label">NIM</label>
																<input type="text" name="nim" class="form-control" value="' . htmlspecialchars($row['nim']) . '" required>
															</div>

															<div class="mb-3">
																<label class="form-label">Nama</label>
																<input type="text" name="nama" class="form-control" value="' . htmlspecialchars($row['nama']) . '" required>
															</div>

															<div class="mb-3">
																<label class="form-label">Program Studi</label>
																<input type="text" name="prodi" class="form-control" value="' . htmlspecialchars($row['prodi']) . '" required>
															</div>

															<div class="mb-3">
																<label class="form-label">Angkatan</label>
																<input type="number" name="angkatan" class="form-control" value="' . htmlspecialchars($row['angkatan']) . '" min="2000" max="2100" required>
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
									echo '<tr><td colspan="6" class="text-center text-muted">Belum ada data mahasiswa</td></tr>';
								}
								?>
							</tbody>
						</table>
					</div>

					<!-- Info jumlah data -->
					<?php
					$total = $mhs->jumlahData();
					?>
					<div class="text-muted small">
						<i class="bi bi-info-circle me-1"></i>Total: <?= $total ?> mahasiswa
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
						<i class="bi bi-plus-circle me-2"></i>Tambah Mahasiswa
					</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
				</div>

				<form method="POST" action="index.php">
					<div class="modal-body">
						<div class="mb-3">
							<label class="form-label">NIM <span class="text-danger">*</span></label>
							<input type="text" name="nim" class="form-control" placeholder="Masukkan NIM" required>
						</div>

						<div class="mb-3">
							<label class="form-label">Nama <span class="text-danger">*</span></label>
							<input type="text" name="nama" class="form-control" placeholder="Masukkan Nama Lengkap" required>
						</div>

						<div class="mb-3">
							<label class="form-label">Program Studi <span class="text-danger">*</span></label>
							<input type="text" name="prodi" class="form-control" placeholder="Masukkan Program Studi" required>
						</div>

						<div class="mb-3">
							<label class="form-label">Angkatan <span class="text-danger">*</span></label>
							<input type="number" name="angkatan" class="form-control" placeholder="Contoh: 2024" min="2000" max="2100" required>
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