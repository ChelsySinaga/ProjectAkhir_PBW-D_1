<?php
include 'koneksi.php';
include 'sesi.php';

// Cek apakah user adalah mahasiswa
if ($_SESSION['role'] !== 'mahasiswa') {
    echo "<script>alert('Akses ditolak'); location.href='dashboard.php';</script>";
    exit;
}

// Validasi ID tugas
$id_tugas = $_GET['id'] ?? null;
$mahasiswa_id = $_SESSION['user_id'];

if (!$id_tugas || !is_numeric($id_tugas)) {
    echo "<script>alert('ID tugas tidak valid'); location.href='dashboard.php';</script>";
    exit;
}

// Ambil data jawaban
$stmt = mysqli_prepare($koneksi, "SELECT * FROM jawaban_mahasiswa WHERE id_tugas = ? AND mahasiswa_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id_tugas, $mahasiswa_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$jawaban = mysqli_fetch_assoc($result);

if (!$jawaban) {
    echo "<script>alert('Belum mengumpulkan jawaban.'); location.href='dashboard.php';</script>";
    exit;
}

// Cek apakah sudah final
if ($jawaban['status'] === 'final') {
    echo "<script>alert('Jawaban sudah diserahkan.'); location.href='dashboard.php';</script>";
    exit;
}

// Update status ke final
$stmt_update = mysqli_prepare($koneksi, "UPDATE jawaban_mahasiswa SET status = 'final' WHERE id = ?");
mysqli_stmt_bind_param($stmt_update, "i", $jawaban['id']);
$berhasil = mysqli_stmt_execute($stmt_update);

if ($berhasil) {
    echo "<script>alert('Jawaban berhasil diserahkan.'); location.href='dashboard.php';</script>";
} else {
    echo "<script>alert('Gagal menyerahkan jawaban.'); location.href='dashboard.php';</script>";
}
?>

