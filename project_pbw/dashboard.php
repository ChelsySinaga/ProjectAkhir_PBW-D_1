<?php
include 'koneksi.php';
include 'sesi.php';

$nama = $_SESSION['nama'];
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - <?= ucfirst($role) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background:rgb(125, 230, 207);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .navbar {
        background: linear-gradient(to right,rgb(28, 10, 229), #fda085);
    }
    .navbar-brand {
        font-weight: bold;
        color: #fff;
    }
    .card {
        border: none;
        border-radius: 1rem;
    }
    .table {
        background: #fff;
        border-radius: 0.75rem;
    }
    .btn {
        border-radius: 0.75rem;
    }
    h4 {
        color: #333;
        font-weight: 600;
    }

    /* Revisi: kolom selain deskripsi tidak membungkus teks */
    td:not(:nth-child(3)),
    th:not(:nth-child(3)) {
        white-space: nowrap;
    }
</style>

</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Dashboard <?= ucfirst($role) ?></a>
    <div class="text-white">
        Halo, <strong><?= htmlspecialchars($nama) ?></strong>
        <a href="logout.php" class="btn btn-sm btn-outline-light ms-3">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">

<?php if ($role == 'dosen'): ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Daftar Tugas yang Anda Buat</h4>
        <a href="buat_tugas.php" class="btn btn-warning">+ Buat Tugas Baru</a>
    </div>

    <div class="card shadow p-3">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-warning">
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Deadline</th>
                        <th>File</th>
                        <th>Tanggal Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $no = 1;
                    $query = mysqli_query($koneksi, "SELECT * FROM tugas_dosen WHERE dosen_id = $user_id ORDER BY deadline ASC");
                    while ($tugas = mysqli_fetch_assoc($query)) {
                        echo "<tr>
                        <td>$no</td>
                        <td>".htmlspecialchars($tugas['judul'])."</td>
                        <td>".date('d/m/Y H:i', strtotime($tugas['deadline']))."</td>
                        <td><a href='uploads/{$tugas['file_soal']}' target='_blank'>{$tugas['file_asli']}</a></td>
                        <td>{$tugas['tanggal_upload']}</td>
                        <td>
                            <a href='detail_tugas.php?id={$tugas['id']}' class='btn btn-sm btn-info'>Lihat Jawaban</a>
                            <a href='edit_tugas.php?id={$tugas['id']}' class='btn btn-sm btn-secondary'>Edit</a>
                            <a href='hapus_tugas.php?id={$tugas['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin ingin menghapus tugas ini?')\">Hapus</a>
                        </td>
                        </tr>";
                        $no++;
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($role == 'mahasiswa'): ?>
    <h4 class="mb-3">Daftar Tugas dari Dosen</h4>
    <div class="card shadow p-3">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-warning">
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Deadline</th>
                        <th>File</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                $now = date('Y-m-d H:i:s');
                $query = mysqli_query($koneksi, "SELECT * FROM tugas_dosen ORDER BY deadline ASC");
                while ($tugas = mysqli_fetch_assoc($query)) {
                    $cek = mysqli_query($koneksi, "SELECT * FROM jawaban_mahasiswa WHERE id_tugas = {$tugas['id']} AND mahasiswa_id = $user_id");
                    $sudah_kumpul = mysqli_num_rows($cek) > 0;
                    $deadline = $tugas['deadline'];

                    echo "<tr>
                        <td>$no</td>
                        <td>".htmlspecialchars($tugas['judul'])."</td>
                        <td>".htmlspecialchars($tugas['deskripsi'])."</td>
                        <td>" . date('d/m/Y H:i', strtotime($deadline)) . "</td>
                        <td><a href='uploads/{$tugas['file_soal']}' target='_blank'>{$tugas['file_asli']}</a></td>
                        <td>";

                    if ($sudah_kumpul) {
                        $jawaban = mysqli_fetch_assoc($cek);
                        $status = $jawaban['status'];

                        if ($status === 'final') {
                            echo "<span class='badge bg-success'>Terkumpul</span>";
                        } else {
                            if (strtotime($deadline) >= strtotime($now)) {
                                echo "<a href='edit_jawaban.php?id={$tugas['id']}' class='btn btn-sm btn-warning me-1'>Edit</a>";
                                echo "<a href='serahkan_jawaban.php?id={$tugas['id']}' class='btn btn-sm btn-primary me-1' onclick=\"return confirm('Setelah diserahkan, Anda tidak bisa mengedit lagi. Yakin?')\">Serahkan</a>";
                                echo "<a href='hapus_jawaban.php?id={$tugas['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin ingin menghapus jawaban ini?')\">Hapus</a>";
                            } else {
                                echo "<span class='badge bg-success'>Terkumpul</span>";
                            }
                        }
                    } elseif (strtotime($deadline) < strtotime($now)) {
                        echo "<span class='badge bg-secondary'>Terlambat</span>";
                    } else {
                        echo "<a href='kumpul_jawaban.php?id={$tugas['id']}' class='btn btn-sm btn-success'>Kumpulkan</a>";
                    }

                    echo "</td></tr>";
                    $no++;
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
