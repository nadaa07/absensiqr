<?php

$host = "localhost";
$dbname = "qr_absensi";
$username = "if0_42975007";
$password = "tUZIMe3AQa";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE =>
                PDO::ERRMODE_EXCEPTION,

            PDO::ATTR_DEFAULT_FETCH_MODE =>
                PDO::FETCH_ASSOC,

            PDO::ATTR_EMULATE_PREPARES =>
                false
        ]
    );

} catch (PDOException $e) {

    die(
        "Koneksi database gagal: " .
        $e->getMessage()
    );

}