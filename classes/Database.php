<?php

// Class untuk mengatur koneksi database
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "dunaman";
    private $db   = "workshop_aslab";
    public $koneksi;

    // Konstruktor: dijalankan saat class dipanggil
    public function __construct() {
        $this->koneksi = mysqli_connect($this->host, $this->user, $this->pass, $this->db);

        // Cek jika koneksi gagal
        if (!$this->koneksi) {
            die("Koneksi gagal: " . mysqli_connect_error());
        }
    }

    // Mengambil objek koneksi
    public function getConnection() {
        return $this->koneksi;
    }
}
