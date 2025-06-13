<?php
include 'koneksi.php';
include 'sesi.php';

if ($_SESSION['role'] !== 'dosen') {
    echo "<script>alert('Akses ditolak.'); location.href='dashboard.php';</script>";
    exit;
}

$id_tugas  = $_GET['id'] ?? null;
$dosen_id = $_SESSION['user_id'];

// Cek data tugas
$tugas = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tugas_dosen WHERE id='$id_tugas' AND dosen_id='$dosen_id'"));

if (!$tugas) {
    echo "<script>alert('Tugas tidak ditemukan atau bukan milik Anda.'); location.href='dashboard.php';</script>";
    exit;
}

// Hapus file soal
if (!empty($tugas['file_soal']) && file_exists("uploads/{$tugas['file_soal']}")) {
    unlink("uploads/{$tugas['file_soal']}");
}

// Hapus tugas dari database
mysqli_query($koneksi, "DELETE FROM tugas_dosen WHERE id='$id_tugas'");

// Opsional: juga hapus semua jawaban mahasiswa terkait tugas ini
mysqli_query($koneksi, "DELETE FROM jawaban_mahasiswa WHERE id_tugas='$id_tugas'");

header("Location: dashboard.php?pesan=Tugas berhasil dihapus");
exit;
