<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to be logged in to enroll.";
    exit;
}

// Database connection
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

// Check if course_id is passed
if (isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];
    $user_id = $_SESSION['user_id'];  // Get the logged-in user's ID

    // Check if the user is already enrolled in the course
    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = :user_id AND course_id = :course_id");
    $stmt->execute(['user_id' => $user_id, 'course_id' => $course_id]);
    
    if ($stmt->rowCount() > 0) {
        echo "You are already enrolled in this course.";
    } else {
        // Enroll the user into the course
        $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id, enrolled_at) VALUES (:user_id, :course_id, NOW())");
        if ($stmt->execute(['user_id' => $user_id, 'course_id' => $course_id])) {
            echo "Successfully enrolled in the course!";
        } else {
            echo "There was an error enrolling you in the course.";
        }
    }
} else {
    echo "Course ID is missing.";
}
?>
