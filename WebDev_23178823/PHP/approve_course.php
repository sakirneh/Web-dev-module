<?php

// Database connection
session_start();
$host = 'localhost';
$db = 'logic launch';
$user = 'root';
$pass = '';

// Initialize PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if course ID is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $course_id = intval($_POST['id']);

    // Ensure the user is logged in and is an admin
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') {
        $admin_id = $_SESSION['user_id']; // Retrieve admin ID from session

        // Update the course approval status and approved_by_admin_id
        $stmt = $conn->prepare("UPDATE content SET is_approved = 1, approved_by_admin_id = ? WHERE course_id = ?");
        $stmt->bind_param("ii", $admin_id, $course_id);

        if ($stmt->execute()) {
            echo "Course approved successfully!";
        } else {
            echo "Error approving course.";
        }

        $stmt->close();
    } else {
        echo "Access denied. Only administrators can approve courses.";
    }
}

$conn->close();
?>
