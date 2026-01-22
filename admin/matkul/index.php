<?php
session_start();
include '../../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['username'])) {
	header("location: ../../auth/login.php");
	exit();
}

// Cek level admin
if ($_SESSION['level'] != 'admin') {
	header("location: ../../index.php");
	exit();
}

$pesan = '';
$tipe_pesan = '';

// Tambah Data
if (isset($_POST['tambah'])) {
	$kode_matkul = mysqli_real_escape_string($koneksi, $_POST['kode_matkul']);
	$nama_matkul = mysqli_real_escape_string($koneksi, $_POST['nama_matkul']);
	$sks = mysqli_real_escape_string($koneksi, $_POST['sks']);

	// Cek apakah kode matkul sudah ada
	$cek = mysqli_query($koneksi, "SELECT * FROM matkul WHERE kode_matkul='$kode_matkul'");
	if (mysqli_num_rows($cek) > 0) {
		$pesan = "Kode Mata Kuliah sudah terdaftar!";
		$tipe_pesan = "danger";
	} else {
		$query = "INSERT INTO matkul (kode_matkul, nama_matkul, sks) VALUES ('$kode_matkul', '$nama_matkul', '$sks')";
		if (mysqli_query($koneksi, $query)) {
			$pesan = "Data mata kuliah berhasil ditambahkan!";
			$tipe_pesan = "success";
		} else {
			$pesan = "Gagal menambahkan data: " . mysqli_error($koneksi);
			$tipe_pesan = "danger";
		}
	}
}

// Hapus Data
if (isset($_GET['hapus'])) {
	$kode_matkul = mysqli_real_escape_string($koneksi, $_GET['hapus']);
	if (mysqli_query($koneksi, "DELETE FROM matkul WHERE kode_matkul='$kode_matkul'")) {
		$pesan = "Data mata kuliah berhasil dihapus!";
		$tipe_pesan = "success";
	} else {
		$pesan = "Gagal menghapus data!";
		$tipe_pesan = "danger";
	}
}

// Update Data
if (isset($_POST['update'])) {
	$kode_lama = mysqli_real_escape_string($koneksi, $_POST['kode_lama']);
	$kode_matkul = mysqli_real_escape_string($koneksi, $_POST['kode_matkul']);
	$nama_matkul = mysqli_real_escape_string($koneksi, $_POST['nama_matkul']);
	$sks = mysqli_real_escape_string($koneksi, $_POST['sks']);

	$query = "UPDATE matkul SET kode_matkul='$kode_matkul', nama_matkul='$nama_matkul', sks='$sks' WHERE kode_matkul='$kode_lama'";
	if (mysqli_query($koneksi, $query)) {
		$pesan = "Data mata kuliah berhasil diupdate!";
		$tipe_pesan = "success";
	} else {
		$pesan = "Gagal mengupdate data: " . mysqli_error($koneksi);
		$tipe_pesan = "danger";
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Data Mata Kuliah | SIA</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body>
	<?php include '../../includes/sidebar.php'; ?>

	<div class="main-content">
		<div class="top-navbar">
			<h5 class="page-title">
				<i class="bi bi-book-fill text-primary me-2"></i>Data Mata Kuliah
			</h5>
			<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
				<i class="bi bi-plus-circle me-1"></i> Tambah Matkul
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
									<th width="15%">Kode Matkul</th>
									<th width="45%">Nama Mata Kuliah</th>
									<th width="10%">SKS</th>
									<th width="25%">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$no = 1;
								$data = mysqli_query($koneksi, "SELECT * FROM matkul ORDER BY nama_matkul ASC");

								if (mysqli_num_rows($data) > 0) {
									while ($row = mysqli_fetch_assoc($data)) {
										echo "<tr>
											<td class='text-center'>" . $no++ . "</td>
											<td><code>" . htmlspecialchars($row['kode_matkul']) . "</code></td>
											<td>" . htmlspecialchars($row['nama_matkul']) . "</td>
											<td class='text-center'><span class='badge bg-primary'>" . htmlspecialchars($row['sks']) . " SKS</span></td>
											<td class='text-center'>
												<button class='btn btn-warning btn-sm me-1' data-bs-toggle='modal' data-bs-target='#modalEdit" . $row['kode_matkul'] . "'>
													<i class='bi bi-pencil-square'></i> Edit
												</button>
												<a href='index.php?hapus=" . $row['kode_matkul'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus mata kuliah " . htmlspecialchars($row['nama_matkul']) . "?')\">
													<i class='bi bi-trash'></i> Hapus
												</a>
											</td>
										</tr>";

										// Modal Edit
										echo '
										<div class="modal fade" id="modalEdit' . $row['kode_matkul'] . '" tabindex="-1">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-header bg-warning">
														<h5 class="modal-title">
															<i class="bi bi-pencil-square me-2"></i>Edit Mata Kuliah
														</h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
													</div>

													<form method="POST" action="index.php">
														<div class="modal-body">
															<input type="hidden" name="kode_lama" value="' . $row['kode_matkul'] . '">

															<div class="mb-3">
																<label class="form-label">Kode Mata Kuliah</label>
																<input type="text" name="kode_matkul" class="form-control" value="' . htmlspecialchars($row['kode_matkul']) . '" maxlength="10" required>
															</div>

															<div class="mb-3">
																<label class="form-label">Nama Mata Kuliah</label>
																<input type="text" name="nama_matkul" class="form-control" value="' . htmlspecialchars($row['nama_matkul']) . '" required>
															</div>

															<div class="mb-3">
																<label class="form-label">SKS</label>
																<input type="number" name="sks" class="form-control" value="' . htmlspecialchars($row['sks']) . '" min="1" max="6" required>
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
									echo '<tr><td colspan="5" class="text-center text-muted">Belum ada data mata kuliah</td></tr>';
								}
								?>
							</tbody>
						</table>
					</div>

					<!-- Info jumlah data -->
					<?php
					$total = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM matkul"));
					$total_sks = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(sks) as total FROM matkul"))['total'] ?? 0;
					?>
					<div class="text-muted small">
						<i class="bi bi-info-circle me-1"></i>
						Total: <?= $total ?> mata kuliah (<?= $total_sks ?> SKS)
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
						<i class="bi bi-plus-circle me-2"></i>Tambah Mata Kuliah
					</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
				</div>

				<form method="POST" action="index.php">
					<div class="modal-body">
						<div class="mb-3">
							<label class="form-label">Kode Mata Kuliah <span class="text-danger">*</span></label>
							<input type="text" name="kode_matkul" class="form-control" placeholder="Contoh: MK001" maxlength="10" required>
						</div>

						<div class="mb-3">
							<label class="form-label">Nama Mata Kuliah <span class="text-danger">*</span></label>
							<input type="text" name="nama_matkul" class="form-control" placeholder="Masukkan nama mata kuliah" required>
						</div>

						<div class="mb-3">
							<label class="form-label">SKS <span class="text-danger">*</span></label>
							<input type="number" name="sks" class="form-control" placeholder="Jumlah SKS" min="1" max="6" required>
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
