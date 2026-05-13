<?php
    $host = "localhost";
    $username = "root";
    $pass = "";
    $dbname = "praktikum_login";

    $conn = new mysqli($host, $username, $pass, $dbname);

    if ($conn->connect_error) {
        die("Koneksi Gagal: " . $conn->connect_error);
    }
?>