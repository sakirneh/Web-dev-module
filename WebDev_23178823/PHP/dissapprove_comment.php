<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'logic launch';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);


// Check if the comment ID is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $comment_id = intval($_POST['id']);

    // Ensure the user is an admin
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') {
        $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->bind_param("i", $comment_id);

        if ($stmt->execute()) {
            echo "Comment disapproved successfully!";
        } else {
            echo "Error disapproving comment.";
        }

        $stmt->close();
    } else {
        echo "Access denied. Only administrators can disapprove comments.";
    }
}

$conn->close();
?>
