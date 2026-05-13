<?php
    session_start();
    require_once 'koneksi.php';

    if($_SESSION['role'] !== 'admin') {
        header("Location: dashboard.php");
        exit();
    }

    $data = null;
    if (isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        $res = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
        $data = mysqli_fetch_assoc($res);
    }
    
    $pesan = "";
    if (isset($_POST['edit'])){
        $old_nama = $data['nama'];
        $nama = isset($_POST['nama']) ? ($_POST['nama']) : '';
        $password = isset($_POST['password']) ? ($_POST['password']) : '';
        if (empty($nama)) {
            $pesan = "Update Gagal: Nama wajib diisi.";
        } elseif ($nama == $old_nama && empty($password)) {
            $pesan = "Update Gagal: Tidak terjadi perubahan.";
        } elseif (!empty($password) && strlen($password) < 6) {
            $pesan = "Validasi Gagal: Password minimal 6 karakter.";
        } elseif (empty($password)) {
            $nama = mysqli_real_escape_string($conn, $_POST['nama']);
            mysqli_query($conn, "UPDATE users SET nama='$nama' WHERE id=$id");
            header("Location: dashboard.php");
            exit();
        } else {
            $nama = mysqli_real_escape_string($conn, $_POST['nama']);
            $password = mysqli_real_escape_string($conn, $_POST['password']);
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            mysqli_query($conn, "UPDATE users SET nama='$nama', password='$hashed_password' WHERE id=$id");
            header("Location: dashboard.php");
            exit();
        }
    }

    if (isset($_POST['back'])){
        header("Location: dashboard.php");
        exit();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
</head>
<body>
    <h1>Edit data pengguna</h1>
    <form method="POST">
        <label for="nama">Nama:</label><br>
        <input type="text" id="nama" name="nama" value="<?= $data['nama'] ?>" required><br><br>
        <label for="password">Password Baru: (Kosongkan jika tidak ingin mengubah)</label><br>
        <input type="password" id="password" name="password" placeholder="Masukkan password baru"><br><br>
        <button type="submit" name="edit">Simpan Perubahan</button>
        <?php if ($pesan != "") echo "<h3>$pesan</h3>"; ?>
    </form>
    <br>
    <button type="button" onclick="window.location.href='dashboard.php'">Batal</button>
</body>
</html>