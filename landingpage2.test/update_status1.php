<?php
require 'config/db.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id = $_POST['id'];
$status = $_POST['status'];

$stmt = $pdo->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

header("Location: dashboard.php");
exit;
