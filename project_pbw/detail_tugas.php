<?php
include 'koneksi.php';
include 'sesi.php';

if ($_SESSION['role'] !== 'dosen') {
    echo "<script>alert('Akses ditolak.'); location.href='dashboard.php';</script>";
    exit;
}

$id_tugas = $_GET['id'] ?? null;
if (!$id_tugas) {
    echo "<script>alert('Tugas tidak ditemukan.'); location.href='dashboard.php';</script>";
    exit;
}

$tugas = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tugas_dosen WHERE id='$id_tugas'"));

if (!$tugas) {
    echo "<script>alert('Data tugas tidak valid.'); location.href='dashboard.php';</script>";
    exit;
}

$query = mysqli_query($koneksi, "
    SELECT jm.*, u.nama 
    FROM jawaban_mahasiswa jm
    JOIN users u ON jm.mahasiswa_id = u.id
    WHERE jm.id_tugas = '$id_tugas' AND jm.status = 'final'
    ORDER BY jm.tanggal_upload ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Pengumpulan Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f8ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 900px;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        h4 {
            font-weight: 600;
            color: #333;
        }
        .btn-secondary {
            border-radius: 0.75rem;
        }
        .table {
            border-radius: 0.75rem;
            overflow: hidden;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h4 class="mb-4 text-center text-primary">Pengumpulan Tugas: <?= htmlspecialchars($tugas['judul']) ?></h4>

    <div class="card p-4 mb-4">
        <p><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($tugas['deskripsi'])) ?></p>
        <p><strong>Deadline:</strong> <?= date('d/m/Y H:i', strtotime($tugas['deadline'])) ?></p>
        <p><strong>File Soal:</strong> <a href="uploads/<?= $tugas['file_soal'] ?>" target="_blank"><?= $tugas['file_asli'] ?></a></p>
    </div>

    <?php if (mysqli_num_rows($query) === 0): ?>
        <div class="alert alert-info text-center">Belum ada mahasiswa yang menyerahkan tugas ini.</div>
    <?php else: ?>
        <div class="card p-4">
            <h5 class="mb-3 text-success">Daftar Mahasiswa yang Menyerahkan</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Nama Mahasiswa</th>
                            <th>File Jawaban</th>
                            <th>Tanggal Upload</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query)) {
                            echo "<tr>
                                <td>{$no}</td>
                                <td>".htmlspecialchars($row['nama'])."</td>
                                <td><a href='uploads/{$row['file_jawaban']}' target='_blank'>".htmlspecialchars($row['file_asli'])."</a></td>
                                <td>" . date('d/m/Y H:i', strtotime($row['tanggal_upload'])) . "</td>
                                </tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <div class="mt-4 text-center">
        <a href="dashboard.php" class="btn btn-secondary">⬅ Kembali ke Dashboard</a>
    </div>
</div>

</body>
</html>
