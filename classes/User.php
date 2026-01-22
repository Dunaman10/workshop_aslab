<?php

// Class untuk mengelola data User (Admin, Dosen, Mahasiswa)
class User {
    private $db;
    private $conn;

    // Konstruktor menerima objek Database
    public function __construct($database) {
        $this->db = $database;
        $this->conn = $this->db->getConnection();
    }

    // Menampilkan semua data user diurutkan berdasarkan level dan username
    public function tampil() {
        $query = "SELECT * FROM users ORDER BY level, username ASC";
        return mysqli_query($this->conn, $query);
    }

    // Menambah user baru
    public function tambah($username, $password, $level) {
        // Membersihkan data input
        $username = mysqli_real_escape_string($this->conn, $username);
        $level = mysqli_real_escape_string($this->conn, $level);

        // Cek apakah username sudah ada
        $cek = mysqli_query($this->conn, "SELECT * FROM users WHERE username='$username'");
        if (mysqli_num_rows($cek) > 0) {
            return "Username sudah terdaftar!";
        }

        // Enkripsi password sebelum disimpan
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, password, level) VALUES ('$username', '$password_hash', '$level')";
        
        if (mysqli_query($this->conn, $query)) {
            return true;
        } else {
            return "Gagal menambahkan data: " . mysqli_error($this->conn);
        }
    }

    // Mengubah data user
    public function update($id, $username, $password, $level) {
        // Membersihkan data input
        $id = mysqli_real_escape_string($this->conn, $id);
        $username = mysqli_real_escape_string($this->conn, $username);
        $level = mysqli_real_escape_string($this->conn, $level);

        // Jika password diisi, update password dan enkripsi ulang
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET username='$username', password='$password_hash', level='$level' WHERE id='$id'";
        } else {
            // Jika password kosong, hanya update username dan level
            $query = "UPDATE users SET username='$username', level='$level' WHERE id='$id'";
        }

        if (mysqli_query($this->conn, $query)) {
            return true;
        } else {
            return "Gagal mengupdate data: " . mysqli_error($this->conn);
        }
    }

    // Menghapus user
    public function hapus($id, $current_username) {
        $id = mysqli_real_escape_string($this->conn, $id);

        // Cek user yang akan dihapus
        $cek_user = mysqli_query($this->conn, "SELECT username FROM users WHERE id='$id'");
        $user_hapus = mysqli_fetch_assoc($cek_user);

        // Mencegah penghapusan akun yang sedang login
        if ($user_hapus && $user_hapus['username'] == $current_username) {
            return "Tidak dapat menghapus akun sendiri!";
        }

        if (mysqli_query($this->conn, "DELETE FROM users WHERE id='$id'")) {
            return true;
        } else {
            return "Gagal menghapus data: " . mysqli_error($this->conn);
        }
    }

    // Menghitung total data user
    public function jumlahData() {
        return mysqli_num_rows(mysqli_query($this->conn, "SELECT * FROM users"));
    }

    // Menghitung data user berdasarkan level (Admin, Dosen, atau Mahasiswa)
    public function jumlahByLevel($level) {
        $level = mysqli_real_escape_string($this->conn, $level);
        return mysqli_num_rows(mysqli_query($this->conn, "SELECT * FROM users WHERE level='$level'"));
    }

    // Mencari user berdasarkan username (untuk fitur lupa password sederhana)
    public function findByUsername($username) {
        $username = mysqli_real_escape_string($this->conn, $username);
        $query = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    // Reset password langsung berdasarkan username
    public function resetPasswordByUsername($username, $new_password) {
        $username = mysqli_real_escape_string($this->conn, $username);
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        $query = "UPDATE users SET password='$password_hash' WHERE username='$username'";
        return mysqli_query($this->conn, $query);
    }
}
