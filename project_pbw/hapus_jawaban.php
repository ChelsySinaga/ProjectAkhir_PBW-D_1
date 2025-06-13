<?php
include 'koneksi.php';
include 'sesi.php';

if ($_SESSION['role'] !== 'mahasiswa') {
    echo "<script>alert('Akses ditolak.'); location.href='dashboard.php';</script>";
    exit;
}

$id_tugas = $_GET['id'] ?? null;
$mahasiswa_id = $_SESSION['user_id'];

if (!$id_tugas || !is_numeric($id_tugas)) {
    echo "<script>alert('ID tugas tidak valid'); location.href='dashboard.php';</script>";
    exit;
}

// Cek jawaban mahasiswa
$cek = mysqli_query($koneksi, "SELECT * FROM jawaban_mahasiswa WHERE id_tugas = $id_tugas AND mahasiswa_id = $mahasiswa_id");
$jawaban = mysqli_fetch_assoc($cek);

if (!$jawaban) {
    echo "<script>alert('Jawaban tidak ditemukan.'); location.href='dashboard.php';</script>";
    exit;
}

// Jangan hapus jika status final
if ($jawaban['status'] === 'final') {
    echo "<script>alert('Jawaban sudah diserahkan dan tidak bisa dihapus.'); location.href='dashboard.php';</script>";
    exit;
}

// Hapus file jika ada
if (!empty($jawaban['file_jawaban']) && file_exists("uploads/{$jawaban['file_jawaban']}")) {
    unlink("uploads/{$jawaban['file_jawaban']}");
}

// Hapus dari database
mysqli_query($koneksi, "DELETE FROM jawaban_mahasiswa WHERE id_tugas = $id_tugas AND mahasiswa_id = $mahasiswa_id");

echo "<script>alert('Jawaban berhasil dihapus.'); location.href='dashboard.php';</script>";
?>
