<?php

// Class untuk mengelola data Mahasiswa
class Mahasiswa {
    private $db;
    private $conn;

    // Konstruktor menerima objek Database
    public function __construct($database) {
        $this->db = $database;
        $this->conn = $this->db->getConnection();
    }

    // Menampilkan semua data mahasiswa
    public function tampil() {
        $query = "SELECT * FROM mahasiswa ORDER BY angkatan DESC, nama ASC";
        $result = mysqli_query($this->conn, $query);
        return $result;
    }

    // Menambah data mahasiswa baru
    public function tambah($nim, $nama, $prodi, $angkatan) {
        // Membersihkan data input untuk keamanan
        $nim = mysqli_real_escape_string($this->conn, $nim);
        $nama = mysqli_real_escape_string($this->conn, $nama);
        $prodi = mysqli_real_escape_string($this->conn, $prodi);
        $angkatan = mysqli_real_escape_string($this->conn, $angkatan);

        // Validasi nama tidak boleh ada angka
        if (preg_match('/[0-9]/', $nama)) {
            return "Nama tidak boleh mengandung angka!";
        }

        // Cek apakah NIM sudah terdaftar
        $cek = mysqli_query($this->conn, "SELECT * FROM mahasiswa WHERE nim='$nim'");
        if (mysqli_num_rows($cek) > 0) {
            return "NIM sudah terdaftar!";
        }

        // Query insert data
        $query = "INSERT INTO mahasiswa (nim, nama, prodi, angkatan) VALUES ('$nim', '$nama', '$prodi', '$angkatan')";
        if (mysqli_query($this->conn, $query)) {
            return true;
        } else {
            return "Gagal menambahkan data: " . mysqli_error($this->conn);
        }
    }

    // Mengubah data mahasiswa
    public function update($nim_lama, $nim, $nama, $prodi, $angkatan) {
        // Membersihkan data input
        $nim_lama = mysqli_real_escape_string($this->conn, $nim_lama);
        $nim = mysqli_real_escape_string($this->conn, $nim);
        $nama = mysqli_real_escape_string($this->conn, $nama);
        $prodi = mysqli_real_escape_string($this->conn, $prodi);
        $angkatan = mysqli_real_escape_string($this->conn, $angkatan);

        // Validasi nama
        if (preg_match('/[0-9]/', $nama)) {
            return "Nama tidak boleh mengandung angka!";
        }

        // Query update data
        $query = "UPDATE mahasiswa SET nim='$nim', nama='$nama', prodi='$prodi', angkatan='$angkatan' WHERE nim='$nim_lama'";
        if (mysqli_query($this->conn, $query)) {
            return true;
        } else {
            return "Gagal mengupdate data: " . mysqli_error($this->conn);
        }
    }

    // Menghapus data mahasiswa
    public function hapus($nim) {
        $nim = mysqli_real_escape_string($this->conn, $nim);
        if (mysqli_query($this->conn, "DELETE FROM mahasiswa WHERE nim='$nim'")) {
            return true;
        } else {
            return "Gagal menghapus data: " . mysqli_error($this->conn);
        }
    }

    // Menghitung jumlah total mahasiswa
    public function jumlahData() {
        return mysqli_num_rows(mysqli_query($this->conn, "SELECT * FROM mahasiswa"));
    }
}
