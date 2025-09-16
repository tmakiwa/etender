<?php
$host = 'localhost';
$db = 'etender';
$user = 'root'; // Set this to your DB user
$pass = ''; // Set this to your DB password

$dsn = "mysql:host=$host;dbname=$db";
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
