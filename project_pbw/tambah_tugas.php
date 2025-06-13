<?php
include 'koneksi.php';
include 'sesi.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul     = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $user_id   = $_SESSION['user_id'];
    $tanggal   = date('Y-m-d');

    $file_name = $_FILES['file']['name'];
    $tmp_file  = $_FILES['file']['tmp_name'];
    $extensi   = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $file_size = $_FILES['file']['size'];
    $allowed   = ['pdf', 'doc', 'docx'];
    $max_size  = 2 * 1024 * 1024;

    if (!in_array($extensi, $allowed)) {
        $error = "Format file harus PDF/DOC/DOCX.";
    } elseif ($file_size > $max_size) {
        $error = "Ukuran file maksimal 2MB.";
    } else {
        $nama_baru = uniqid() . '.' . $extensi;
        if (!is_dir('uploads')) mkdir('uploads');
        move_uploaded_file($tmp_file, 'uploads/' . $nama_baru);

        mysqli_query($koneksi, "INSERT INTO tugas (user_id, judul_tugas, deskripsi, file_tugas, file_asli, tanggal_upload)
                                VALUES ('$user_id', '$judul', '$deskripsi', '$nama_baru', '$file_name', '$tanggal')");

        header("Location: tugas.php?pesan=Tugas berhasil ditambahkan!");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Tugas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f2f4f7;
            min-height: 100vh;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 600px;">
        <div class="card-body">
            <h4 class="mb-3 text-center">Form Tambah Tugas</h4>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Judul Tugas</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload File</label>
                    <input type="file" name="file" class="form-control" required>
                    <small class="text-muted">Format: PDF, DOC, DOCX (maks. 2MB)</small>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Simpan Tugas</button>
                </div>
                <div class="text-center mt-3">
                    <a href="tugas.php" class="btn btn-secondary">Kembali ke Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>