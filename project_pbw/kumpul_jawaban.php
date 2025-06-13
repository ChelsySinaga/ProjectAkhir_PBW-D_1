<?php
include 'koneksi.php';
include 'sesi.php';

// Cek role
if ($_SESSION['role'] !== 'mahasiswa') {
    echo "<script>alert('Akses ditolak.'); location.href='dashboard.php';</script>";
    exit;
}

$id_tugas = $_GET['id'] ?? null;
$mahasiswa_id = $_SESSION['user_id'];

// Ambil data tugas
$query = mysqli_query($koneksi, "SELECT * FROM tugas_dosen WHERE id='$id_tugas'");
$tugas = mysqli_fetch_assoc($query);

if (!$tugas) {
    echo "<script>alert('Tugas tidak ditemukan.'); location.href='dashboard.php';</script>";
    exit;
}

// Cek deadline
$now = date('Y-m-d H:i:s');
$deadline = $tugas['deadline'];
$terlambat = (strtotime($now) > strtotime($deadline));

// Cek apakah sudah mengumpulkan
$cek = mysqli_query($koneksi, "SELECT * FROM jawaban_mahasiswa WHERE id_tugas='$id_tugas' AND mahasiswa_id='$mahasiswa_id'");
$sudah = mysqli_fetch_assoc($cek);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($sudah) {
        $error = "Anda sudah mengumpulkan tugas ini.";
    } elseif ($terlambat) {
        $error = "Batas waktu pengumpulan sudah lewat.";
    } else {
        $file_asli = $_FILES['file']['name'];
        $tmp_file  = $_FILES['file']['tmp_name'];
        $ext       = strtolower(pathinfo($file_asli, PATHINFO_EXTENSION));
        $file_baru = uniqid() . '.' . $ext;

        $allowed = ['pdf', 'doc', 'docx'];
        if (!in_array($ext, $allowed)) {
            $error = "Format file tidak diperbolehkan!";
        } else {
            if (!is_dir('uploads')) mkdir('uploads');
            move_uploaded_file($tmp_file, 'uploads/' . $file_baru);

            $waktu = date('Y-m-d H:i:s');
            mysqli_query($koneksi, "INSERT INTO jawaban_mahasiswa (id_tugas, mahasiswa_id, file_jawaban, file_asli, tanggal_upload)
                                    VALUES ('$id_tugas', '$mahasiswa_id', '$file_baru', '$file_asli', '$waktu')");

            header("Location: dashboard.php?pesan=Tugas berhasil dikumpulkan!");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kumpulkan Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background:rgb(167, 231, 236);
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card shadow mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h4 class="text-center mb-3">Kumpulkan Tugas</h4>

            <div class="mb-3">
                <strong>Judul:</strong> <?= htmlspecialchars($tugas['judul']) ?><br>
                <strong>Deskripsi:</strong> <?= nl2br(htmlspecialchars($tugas['deskripsi'])) ?><br>
                <strong>Deadline:</strong> <?= date('d/m/Y H:i', strtotime($deadline)) ?><br>
                <strong>File Tugas:</strong> <a href="uploads/<?= $tugas['file_soal'] ?>" target="_blank"><?= $tugas['file_asli'] ?></a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($sudah): ?>
                <div class="alert alert-success">Anda sudah mengumpulkan tugas ini.</div>
            <?php elseif ($terlambat): ?>
                <div class="alert alert-warning">Pengumpulan ditutup karena sudah lewat deadline.</div>
            <?php else: ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Upload Jawaban</label>
                        <input type="file" name="file" class="form-control" required>
                        <small class="text-muted">Format: PDF, DOC, DOCX</small>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Kumpulkan</button>
                    </div>
                </form>
            <?php endif; ?>

            <div class="text-center mt-3">
                <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
