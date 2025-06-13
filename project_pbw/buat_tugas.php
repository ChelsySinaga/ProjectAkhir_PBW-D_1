<?php
include 'koneksi.php';
include 'sesi.php';

// Hanya dosen yang boleh akses
if ($_SESSION['role'] !== 'dosen') {
    echo "<script>alert('Akses ditolak!'); location.href='tugas.php';</script>";
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $deadline = $_POST['deadline']; // Format: 2025-06-13T15:00
    $deadline = str_replace('T', ' ', $deadline); // Jadi: 2025-06-13 15:00

    $nama_file_asli = $_FILES['file']['name'];
    $tmp_file       = $_FILES['file']['tmp_name'];
    $ext            = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));
    $nama_baru      = uniqid() . '.' . $ext;

    $folder = 'uploads/';
    if (!is_dir($folder)) mkdir($folder);

    if (move_uploaded_file($tmp_file, $folder . $nama_baru)) {
        $tanggal = date('Y-m-d H:i:s');
        $dosen_id = $_SESSION['user_id'];

        mysqli_query($koneksi, "INSERT INTO tugas_dosen (judul, deskripsi, deadline, file_soal, file_asli, tanggal_upload, dosen_id)
                                VALUES ('$judul', '$deskripsi', '$deadline', '$nama_baru', '$nama_file_asli', '$tanggal', '$dosen_id')");

        header("Location: dashboard.php?pesan=Tugas berhasil dibuat!");
        exit;

    } else {
        $error = "Gagal upload file tugas.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buat Tugas - Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f8;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 600px;">
        <div class="card-body">
            <h4 class="mb-3 text-center">Buat Tugas Baru</h4>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Judul Tugas</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deadline</label>
                    <input type="datetime-local" name="deadline" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">File Soal</label>
                    <input type="file" name="file" class="form-control" required>
                    <small class="text-muted">Format PDF/DOC/DOCX</small>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Simpan Tugas</button>
                </div>
                <div class="text-center mt-3">
                    <a href="dashboard.php?role=dosen" class="btn btn-secondary">Kembali ke Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
