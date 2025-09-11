<?php
require_once __DIR__ . '/../app/config/database.php';

//$departemen_id = "3";
$name = "Admin";
$email = "admin@example.com";
$plainPassword = "admin123";
$role = "admin";

// hash password
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (departemen_id, nama, email, password, role) 
        VALUES (NULL, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$name, $email, $hashedPassword, $role]);

echo "User berhasil dibuat!";
