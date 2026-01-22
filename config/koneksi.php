<?php
$host = "localhost";
$user = "root";
$pass = "dunaman";
$db   = "workshop_aslab";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
} else {
    // echo "Koneksi berhasil";
}
