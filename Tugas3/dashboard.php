<?php
session_start();
require_once 'koneksi.php';
$isAdmin = false;

if (!isset($_SESSION['nama'])) {
    header("Location: dashboard.php");
    exit();
}
$isAdmin = $_SESSION['role'] === 'admin';

$data = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");

if (isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    header("Location: dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>

    <h1>Selamat Datang, <?php echo($_SESSION['role']); ?>!</h1>
    <a href="logout.php"><button>Logout</button></a>
    <hr>
    <?php if ($isAdmin): ?>
        <h2>Menu Admin: Kelola Pengguna</h2>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th colspan="2">Aksi</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($data)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nama']; ?></td>
                <td><a href="edit.php?id=<?= $row['id'] ?>"><button>Edit</button></a>
                <a href="dashboard.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')"><button>Hapus</button></a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>

</body>
</html>