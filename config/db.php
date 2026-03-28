<?php
$host = "192.168.44.42";
$dbname = "kurir";
$user = "opensid";
$pass = "passwordku";

// PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

