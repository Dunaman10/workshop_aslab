<?php
// Menggunakan file Database.php dari folder classes sebagai sumber utama
// Menggunakan __DIR__ agar path relatif tetap benar dimanapun file ini di-include
require_once __DIR__ . '/../classes/Database.php';

// Inisialisasi object Database
$dbObject = new Database();
$koneksi = $dbObject->koneksi;

// Cek apakah konstanta BASE_URL sudah didefinisikan untuk mencegah redefinition
if (!defined('BASE_URL')) {
    // Deteksi protokol (http/https)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    
    // Deteksi host
    $host = $_SERVER['HTTP_HOST'];
    
    define('BASE_URL', $protocol . "://" . $host);
}
?>
