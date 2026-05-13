<?php
session_start();

// Cek apakah user sudah login, jika ya langsung arahkan ke dashboard
if (isset($_SESSION['nama'])) {
    header("Location: dashboard.php");
    exit();
}

$isLogin = true;
if (isset($_GET['action']) && $_GET['action'] == 'register') {
    $isLogin = false;
}

require 'koneksi.php';
$pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Logika Registrasi ---
    if (isset($_POST['register'])) {
        $nama = trim($_POST['nama']);
        $password = $_POST['password'];
        $role = 'user';

        if (empty($nama) || empty($password)) {
            $pesan = "Validasi Gagal: Nama dan password wajib diisi.";
        } elseif (strlen($password) < 6) {
            $pesan = "Validasi Gagal: Password minimal 6 karakter.";
        } else {
            // Cek apakah nama sudah terdaftar
            $stmt_check = $conn->prepare("SELECT id FROM users WHERE nama = ?");
            $stmt_check->bind_param("s", $nama);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $pesan = "Registrasi Gagal: Nama sudah terdaftar.";
            } else {
                // Proses simpan user baru
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT INTO users (nama, password, role) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $nama, $hashed_password, $role);

                if ($stmt->execute()) {
                    $pesan = "Registrasi Berhasil. Silakan login.";
                } else {
                    $pesan = "Kesalahan Server: " . $stmt->error;
                }
                $stmt->close();
            }
            $stmt_check->close();
        }
    }

    // --- Logika Login ---
    if (isset($_POST['login'])) {
        $nama = trim($_POST['nama']);
        $password = $_POST['password'];

        if (empty($nama) || empty($password)) {
            $pesan = "Validasi Gagal: Nama dan password wajib diisi.";
        } else {
            $stmt = $conn->prepare("SELECT password, role FROM users WHERE nama = ?");
            $stmt->bind_param("s", $nama);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($hashed_password, $role);
                $stmt->fetch();

                if (password_verify($password, $hashed_password)) {
                    // Set session dan arahkan ke dashboard
                    $_SESSION['nama'] = $nama;
                    $_SESSION['role'] = $role;
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $pesan = "Login Gagal: Password salah.";
                }
            } else {
                $pesan = "Login Gagal: Pengguna tidak ditemukan.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <?php if ($isLogin): ?>
        <meta name="description" content="Halaman Login untuk sistem autentikasi">
        <title>Login</title>
    <?php else: ?>
        <meta name="description" content="Halaman Registrasi untuk sistem autentikasi">
        <title>Registrasi</title>
    <?php endif; ?>
</head>
<body>
    <?php if ($pesan != "") echo "<h3>$pesan</h3>"; ?>

    <?php if (!$isLogin): ?>
        <h2>Registrasi</h2>
        <form method="POST" action="">
            <input type="text" name="nama" placeholder="Nama Pengguna" required><br><br>
        <input type="password" name="password" placeholder="Password (Min. 6 Karakter)" required><br><br>
        <button type="submit" name="register">Register</button>
    </form>
    <?php else: ?>
        <h2>Login</h2>
        <form method="POST" action="">
            <input type="text" name="nama" placeholder="Nama Pengguna" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <button type="submit" name="login">Login</button>
        </form>
    <?php endif; ?>
    
    <?php if (!$isLogin): ?>
        <p>Sudah punya akun? 
            <a href="auth.php?action=login">
                <button>Login</button>
            </a>
        </p>
    <?php else: ?>
        <p>Belum punya akun? 
            <a href="auth.php?action=register">
                <button>Register</button>
            </a>
        </p>
    <?php endif; ?>
        
</body>
</html>