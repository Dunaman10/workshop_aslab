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
	$nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
	$kode_matkul = mysqli_real_escape_string($koneksi, $_POST['kode_matkul']);
	$nilai = mysqli_real_escape_string($koneksi, $_POST['nilai']);

	// Cek apakah nilai untuk mahasiswa dan matkul sudah ada
	$cek = mysqli_query($koneksi, "SELECT * FROM nilai WHERE nim='$nim' AND kode_matkul='$kode_matkul'");
	if (mysqli_num_rows($cek) > 0) {
		$pesan = "Nilai untuk mahasiswa dan mata kuliah ini sudah ada!";
		$tipe_pesan = "danger";
	} else {
		// Generate ID manual
		$max_id = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT MAX(id) as max_id FROM nilai"));
		$new_id = ($max_id['max_id'] ?? 0) + 1;
		
		$query = "INSERT INTO nilai (id, nim, kode_matkul, nilai) VALUES ('$new_id', '$nim', '$kode_matkul', '$nilai')";
		if (mysqli_query($koneksi, $query)) {
			$pesan = "Data nilai berhasil ditambahkan!";
			$tipe_pesan = "success";
		} else {
			$pesan = "Gagal menambahkan data: " . mysqli_error($koneksi);
			$tipe_pesan = "danger";
		}
	}
}

// Hapus Data
if (isset($_GET['hapus'])) {
	$id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
	if (mysqli_query($koneksi, "DELETE FROM nilai WHERE id='$id'")) {
		$pesan = "Data nilai berhasil dihapus!";
		$tipe_pesan = "success";
	} else {
		$pesan = "Gagal menghapus data!";
		$tipe_pesan = "danger";
	}
}

// Update Data
if (isset($_POST['update'])) {
	$id = mysqli_real_escape_string($koneksi, $_POST['id']);
	$nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
	$kode_matkul = mysqli_real_escape_string($koneksi, $_POST['kode_matkul']);
	$nilai = mysqli_real_escape_string($koneksi, $_POST['nilai']);

	$query = "UPDATE nilai SET nim='$nim', kode_matkul='$kode_matkul', nilai='$nilai' WHERE id='$id'";
	if (mysqli_query($koneksi, $query)) {
		$pesan = "Data nilai berhasil diupdate!";
		$tipe_pesan = "success";
	} else {
		$pesan = "Gagal mengupdate data: " . mysqli_error($koneksi);
		$tipe_pesan = "danger";
	}
}

// Ambil data mahasiswa untuk dropdown
$mahasiswa_list = mysqli_query($koneksi, "SELECT * FROM mahasiswa ORDER BY nama ASC");

// Ambil data matkul untuk dropdown
$matkul_list = mysqli_query($koneksi, "SELECT * FROM matkul ORDER BY nama_matkul ASC");

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Data Nilai | SIA</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
	<style>
		.badge-a { background: linear-gradient(135deg, #28a745, #218838); }
		.badge-b { background: linear-gradient(135deg, #17a2b8, #138496); }
		.badge-c { background: linear-gradient(135deg, #ffc107, #e0a800); color: #000; }
		.badge-d { background: linear-gradient(135deg, #fd7e14, #e56900); }
		.badge-e { background: linear-gradient(135deg, #dc3545, #c82333); }
	</style>
</head>

<body>
	<?php include '../../includes/sidebar.php'; ?>

	<div class="main-content">
		<div class="top-navbar">
			<h5 class="page-title">
				<i class="bi bi-journal-check text-primary me-2"></i>Data Nilai
			</h5>
			<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
				<i class="bi bi-plus-circle me-1"></i> Tambah Nilai
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
									<th width="25%">Nama Mahasiswa</th>
									<th width="25%">Mata Kuliah</th>
									<th width="10%">Nilai</th>
									<th width="20%">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$no = 1;
								$data = mysqli_query($koneksi, "
									SELECT n.*, m.nama as nama_mahasiswa, mk.nama_matkul 
									FROM nilai n 
									LEFT JOIN mahasiswa m ON n.nim = m.nim 
									LEFT JOIN matkul mk ON n.kode_matkul = mk.kode_matkul 
									ORDER BY m.nama ASC, mk.nama_matkul ASC
								");

								if (mysqli_num_rows($data) > 0) {
									while ($row = mysqli_fetch_assoc($data)) {
										// Badge berdasarkan nilai
										$badge_class = '';
										switch (strtoupper($row['nilai'])) {
											case 'A': $badge_class = 'badge-a'; break;
											case 'B': $badge_class = 'badge-b'; break;
											case 'C': $badge_class = 'badge-c'; break;
											case 'D': $badge_class = 'badge-d'; break;
											case 'E': $badge_class = 'badge-e'; break;
											default: $badge_class = 'bg-secondary';
										}

										echo "<tr>
											<td class='text-center'>" . $no++ . "</td>
											<td>" . htmlspecialchars($row['nim']) . "</td>
											<td>" . htmlspecialchars($row['nama_mahasiswa'] ?? '-') . "</td>
											<td>" . htmlspecialchars($row['nama_matkul'] ?? '-') . "</td>
											<td class='text-center'>
												<span class='badge $badge_class' style='font-size: 1rem;'>" . htmlspecialchars($row['nilai']) . "</span>
											</td>
											<td class='text-center'>
												<button class='btn btn-warning btn-sm me-1' data-bs-toggle='modal' data-bs-target='#modalEdit" . $row['id'] . "'>
													<i class='bi bi-pencil-square'></i> Edit
												</button>
												<a href='index.php?hapus=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus nilai ini?')\">
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
															<i class="bi bi-pencil-square me-2"></i>Edit Nilai
														</h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
													</div>

													<form method="POST" action="index.php">
														<div class="modal-body">
															<input type="hidden" name="id" value="' . $row['id'] . '">

															<div class="mb-3">
																<label class="form-label">Mahasiswa</label>
																<select name="nim" class="form-select" required>';
										
										// Reset pointer untuk mahasiswa list
										mysqli_data_seek($mahasiswa_list, 0);
										while ($mhs = mysqli_fetch_assoc($mahasiswa_list)) {
											$selected = ($mhs['nim'] == $row['nim']) ? 'selected' : '';
											echo '<option value="' . $mhs['nim'] . '" ' . $selected . '>' . $mhs['nim'] . ' - ' . htmlspecialchars($mhs['nama']) . '</option>';
										}
										
										echo '								</select>
															</div>

															<div class="mb-3">
																<label class="form-label">Mata Kuliah</label>
																<select name="kode_matkul" class="form-select" required>';
										
										// Reset pointer untuk matkul list
										mysqli_data_seek($matkul_list, 0);
										while ($mk = mysqli_fetch_assoc($matkul_list)) {
											$selected = ($mk['kode_matkul'] == $row['kode_matkul']) ? 'selected' : '';
											echo '<option value="' . $mk['kode_matkul'] . '" ' . $selected . '>' . $mk['kode_matkul'] . ' - ' . htmlspecialchars($mk['nama_matkul']) . '</option>';
										}
										
										echo '								</select>
															</div>

															<div class="mb-3">
																<label class="form-label">Nilai</label>
																<select name="nilai" class="form-select" required>
																	<option value="A" ' . ($row['nilai'] == 'A' ? 'selected' : '') . '>A</option>
																	<option value="B" ' . ($row['nilai'] == 'B' ? 'selected' : '') . '>B</option>
																	<option value="C" ' . ($row['nilai'] == 'C' ? 'selected' : '') . '>C</option>
																	<option value="D" ' . ($row['nilai'] == 'D' ? 'selected' : '') . '>D</option>
																	<option value="E" ' . ($row['nilai'] == 'E' ? 'selected' : '') . '>E</option>
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
									echo '<tr><td colspan="6" class="text-center text-muted">Belum ada data nilai</td></tr>';
								}
								?>
							</tbody>
						</table>
					</div>

					<!-- Info jumlah data -->
					<?php
					$total = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM nilai"));
					?>
					<div class="text-muted small">
						<i class="bi bi-info-circle me-1"></i>Total: <?= $total ?> data nilai
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
						<i class="bi bi-plus-circle me-2"></i>Tambah Nilai
					</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
				</div>

				<form method="POST" action="index.php">
					<div class="modal-body">
						<div class="mb-3">
							<label class="form-label">Mahasiswa <span class="text-danger">*</span></label>
							<select name="nim" class="form-select" required>
								<option value="">-- Pilih Mahasiswa --</option>
								<?php
								mysqli_data_seek($mahasiswa_list, 0);
								while ($mhs = mysqli_fetch_assoc($mahasiswa_list)) {
									echo '<option value="' . $mhs['nim'] . '">' . $mhs['nim'] . ' - ' . htmlspecialchars($mhs['nama']) . '</option>';
								}
								?>
							</select>
						</div>

						<div class="mb-3">
							<label class="form-label">Mata Kuliah <span class="text-danger">*</span></label>
							<select name="kode_matkul" class="form-select" required>
								<option value="">-- Pilih Mata Kuliah --</option>
								<?php
								mysqli_data_seek($matkul_list, 0);
								while ($mk = mysqli_fetch_assoc($matkul_list)) {
									echo '<option value="' . $mk['kode_matkul'] . '">' . $mk['kode_matkul'] . ' - ' . htmlspecialchars($mk['nama_matkul']) . ' (' . $mk['sks'] . ' SKS)</option>';
								}
								?>
							</select>
						</div>

						<div class="mb-3">
							<label class="form-label">Nilai <span class="text-danger">*</span></label>
							<select name="nilai" class="form-select" required>
								<option value="">-- Pilih Nilai --</option>
								<option value="A">A (Sangat Baik)</option>
								<option value="B">B (Baik)</option>
								<option value="C">C (Cukup)</option>
								<option value="D">D (Kurang)</option>
								<option value="E">E (Sangat Kurang)</option>
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
