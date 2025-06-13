<?php
include 'koneksi.php';
include 'sesi.php';

// Pastikan hanya dosen
if ($_SESSION['role'] !== 'dosen') {
    echo "<script>alert('Akses ditolak.'); location.href='dashboard.php';</script>";
    exit;
}

$id_tugas = $_GET['id'] ?? null;
$dosen_id = $_SESSION['user_id'];

// Ambil data tugas
$query = mysqli_query($koneksi, "SELECT * FROM tugas_dosen WHERE id='$id_tugas' AND dosen_id='$dosen_id'");
$tugas = mysqli_fetch_assoc($query);

if (!$tugas) {
    echo "<script>alert('Tugas tidak ditemukan.'); location.href='dashboard.php?role=dosen';</script>";
    exit;
}

// Jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $deadline  = $_POST['deadline'];
    $file_soal = $tugas['file_soal']; // default file lama
    $file_asli = $tugas['file_asli'];

    // Jika ganti file
    if ($_FILES['file']['name']) {
        $nama_asli = $_FILES['file']['name'];
        $tmp       = $_FILES['file']['tmp_name'];
        $ext       = pathinfo($nama_asli, PATHINFO_EXTENSION);
        $nama_baru = uniqid() . '.' . $ext;

        // Hapus file lama jika ada
        if (file_exists("uploads/$file_soal")) {
            unlink("uploads/$file_soal");
        }

        move_uploaded_file($tmp, "uploads/$nama_baru");
        $file_soal = $nama_baru;
        $file_asli = $nama_asli;
    }

    // Update database
    mysqli_query($koneksi, "UPDATE tugas_dosen 
                            SET judul='$judul', deskripsi='$deskripsi', deadline='$deadline', 
                                file_soal='$file_soal', file_asli='$file_asli'
                            WHERE id='$id_tugas'");

    header("Location: dashboard.php?pesan=Tugas berhasil diperbarui&role=dosen");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Tugas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <div class="card shadow mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h4 class="mb-3 text-center">Edit Tugas</h4>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($tugas['judul']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="4" required><?= htmlspecialchars($tugas['deskripsi']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deadline</label>
                    <input type="datetime-local" name="deadline" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($tugas['deadline'])) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">File Soal</label><br>
                    <a href="uploads/<?= $tugas['file_soal'] ?>" target="_blank"><?= $tugas['file_asli'] ?></a>
                    <input type="file" name="file" class="form-control mt-2">
                    <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                </div>
                <div class="text-center mt-3">
                    <a href="dashboard.php?role=dosen" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
