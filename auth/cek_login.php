<?php
session_start();
require '../config/db.php';

$username = $_POST['username'];
$password = hash('sha256', $_POST['password']);

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
$stmt->execute([$username, $password]);
$user = $stmt->fetch();

if ($user) {
    $_SESSION['user'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    header("Location: ../dashboard.php");
    exit;
} else {
    echo "<script>alert('Login gagal!'); window.location.href='../login.php';</script>";
}
