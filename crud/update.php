<?php
include 'koneksi.php';

$id = (int) $_GET['id'];
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $npm = mysqli_real_escape_string($conn, $_POST['npm']);
    mysqli_query($conn, "UPDATE mahasiswa SET nama='$nama', npm='$npm' WHERE id=$id");
    header("Location: index.php");
    exit;
}

$data = mysqli_query($conn, "SELECT * FROM mahasiswa WHERE id=$id");
?>

<!DOCTYPE html>
<html>
<head><title>CRUD Mahasiswa</title></head>
<body>
<h2>Data Mahasiswa</h2>

<form action="update.php?id=<?= $id ?>" method="POST">
    Nama: <input type="text" name="nama" required>
    NPM: <input type="text" name="npm" required>
    <button type="submit" name="update">Update</button>
</form>

<br>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>NPM</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($data)): ?>
    <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= htmlspecialchars($row['npm']) ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>