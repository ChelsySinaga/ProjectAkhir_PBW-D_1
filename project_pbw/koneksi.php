<?php
date_default_timezone_set('Asia/Jakarta');
$host = "localhost";
$user = "root";
$pass = "";
$db = "pengumpulan_tugas_mahasiswa";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
