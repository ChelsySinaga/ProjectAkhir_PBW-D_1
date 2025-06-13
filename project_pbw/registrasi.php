<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role']; // 'mahasiswa' atau 'dosen'

    // Cek apakah email sudah terdaftar
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Email sudah terdaftar!";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query = mysqli_query($koneksi, "INSERT INTO users (nama, email, password, role)
                                         VALUES ('$nama', '$email', '$hash', '$role')");
        if ($query) {
            $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
            header("Location: login.php");
            exit;
        } else {
            $error = "Registrasi gagal. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Pengguna</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            min-height: 100vh;
        }
    </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 100%; max-width: 450px;">
        <h4 class="text-center mb-3">Registrasi Pengguna</h4>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Daftar Sebagai</label>
                <select name="role" class="form-select" required>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success w-100">Daftar</button>
            <div class="text-center mt-3">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
