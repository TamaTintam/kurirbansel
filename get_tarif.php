<?php
require 'config/db.php';

// Ambil tarif pertama
$stmt = $pdo->prepare("SELECT nilai FROM pengaturan WHERE nama = 'tarif_per_km'");
$stmt->execute();
$tarif = (int)$stmt->fetchColumn();

// Ambil tarif tambahan jika lebih dari 5km
$stmt = $pdo->prepare("SELECT nilai FROM pengaturan WHERE nama = 'tarif_lebih'");
$stmt->execute();
$tarif_lebih = (int)$stmt->fetchColumn();

echo json_encode([
  "tarif" => $tarif,
  "tarif_lebih" => $tarif_lebih
]);
