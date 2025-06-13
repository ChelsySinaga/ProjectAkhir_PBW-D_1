<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['login_email'];
    $password = $_POST['login_pass'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='" . mysqli_real_escape_string($koneksi, $email) . "'");
    $user  = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['role']    = $user['role'];

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Pengumpulan Tugas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to right, #74ebd5, #ACB6E5);
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
            <h4 class="text-center mb-3">Login</h4>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <!-- Form dummy untuk cegah autofill -->
            <form style="display: none;">
                <input type="text" name="fakeusernameremembered">
                <input type="password" name="fakepasswordremembered">
            </form>

            <!-- Form utama -->
            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="login_email" class="form-control" placeholder="email@example.com" required autocomplete="off">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="login_pass" class="form-control" placeholder="********" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <div class="text-center mt-3">
                    Belum punya akun? <a href="registrasi.php">Daftar di sini</a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
