<?php

// Database connection
session_start();
$host = 'localhost';
$db = 'logic launch';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if (isset($_POST['first_name'], $_POST['last_name'], $_SESSION['user_id'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name WHERE id = :id");
    $stmt->execute(['first_name' => $first_name, 'last_name' => $last_name, 'id' => $user_id]);

    echo "Name updated successfully!";
}
?>
