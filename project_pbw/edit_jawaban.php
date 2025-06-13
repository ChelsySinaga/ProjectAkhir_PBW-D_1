<?php
include 'koneksi.php';
include 'sesi.php';

// Cek hak akses
if ($_SESSION['role'] !== 'mahasiswa') {
    echo "<script>alert('Akses ditolak.'); location.href='dashboard.php';</script>";
    exit;
}

// Validasi ID tugas
$id_tugas = isset($_GET['id']) ? intval($_GET['id']) : null;
$mahasiswa_id = $_SESSION['user_id'];

if (!$id_tugas || !is_numeric($id_tugas)) {
    echo "<script>alert('ID tugas tidak valid'); location.href='dashboard.php';</script>";
    exit;
}

// Ambil data tugas
$tugas = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tugas_dosen WHERE id = $id_tugas"));
if (!$tugas) {
    echo "<script>alert('Tugas tidak ditemukan'); location.href='dashboard.php';</script>";
    exit;
}

// Ambil jawaban mahasiswa
$jawaban = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM jawaban_mahasiswa WHERE id_tugas = $id_tugas AND mahasiswa_id = $mahasiswa_id"));
if (!$jawaban) {
    echo "<script>alert('Jawaban belum dikumpulkan'); location.href='dashboard.php';</script>";
    exit;
}

// Cek jika jawaban sudah final
if ($jawaban['status'] === 'final') {
    echo "<script>alert('Jawaban sudah diserahkan dan tidak bisa diedit.'); location.href='dashboard.php';</script>";
    exit;
}

// Proses upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_asli = $_FILES['file_jawaban']['name'];
    $tmp_file = $_FILES['file_jawaban']['tmp_name'];
    $ekstensi = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
    $timestamp = date('Ymd_His');
    $nama_baru = "jawaban_{$mahasiswa_id}_{$id_tugas}_{$timestamp}." . $ekstensi;


    $allowed = ['pdf', 'doc', 'docx'];
if (!in_array($ekstensi, $allowed)) {
    echo "<script>alert('Format file tidak diperbolehkan!');</script>";
    exit;
}


    $upload_dir = 'uploads/jawaban/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (move_uploaded_file($tmp_file, $upload_dir . $nama_baru)) {
        $file_lama = $upload_dir . $jawaban['file_jawaban'];
        if (file_exists($file_lama)) {
            unlink($file_lama);
        }

        $query = mysqli_query($koneksi, "
            UPDATE jawaban_mahasiswa 
            SET file_jawaban = '$nama_baru', file_asli = '$nama_asli', tanggal_upload = NOW() 
            WHERE id = {$jawaban['id']}
        ");

        if ($query) {
            echo "<script>alert('Jawaban berhasil diperbarui.'); location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui jawaban.');</script>";
        }
    } else {
        echo "<script>alert('Gagal mengupload file.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Jawaban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e0c3fc, #8ec5fc);
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(to right,rgb(25, 22, 202), #ff6ec4);
            color: white;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }
        .btn-primary {
            background-color:rgb(17, 160, 203);
            border: none;
        }
        .btn-outline-secondary {
            border-color: #ccc;
        }
        .btn-primary:hover {
            background-color: #5010a3;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card p-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Jawaban: <?= htmlspecialchars($tugas['judul']) ?></h5>
            <p class="mt-2"><i class="bi bi-clock-history"></i> Deadline: <?= date('d/m/Y H:i', strtotime($tugas['deadline'])) ?></p>
        </div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="file_jawaban" class="form-label">Upload File Jawaban Baru</label>
                    <input type="file" name="file_jawaban" id="file_jawaban" class="form-control" required>
                    <small class="text-muted">File sebelumnya: <strong><?= htmlspecialchars($jawaban['file_asli']) ?></strong></small>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
