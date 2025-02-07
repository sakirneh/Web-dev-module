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

if (isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'], $_SESSION['user_id'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['user_id'];

    // Fetch the current password from the database
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($current_password === $user['password']) { 
        if ($new_password === $confirm_password) {
            $update_stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $update_stmt->execute(['password' => $new_password, 'id' => $user_id]);
            echo "Password updated successfully!";
        } else {
            echo "New password and confirmation do not match!";
        }
    } else {
        echo "Current password is incorrect!";
    }
}
?>
