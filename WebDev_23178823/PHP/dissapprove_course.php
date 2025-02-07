<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'logic_launch';  
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);


// Check if the course ID is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = intval($_POST['course_id']);
    $user_id = $_SESSION['user_id']; 
    // Ensure the user is an instructor
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Instructor') {
        
        // Check if the course belongs to the instructor
        $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $course_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            
            
            $conn->begin_transaction();

            try {#
                $stmt1 = $conn->prepare("DELETE FROM enrolments WHERE course_id = ?");
                $stmt1->bind_param("i", $course_id);
                $stmt1->execute();
                $stmt1->close();

                $stmt2 = $conn->prepare("DELETE FROM comments WHERE course_id = ?");
                $stmt2->bind_param("i", $course_id);
                $stmt2->execute();
                $stmt2->close();

                $stmt3 = $conn->prepare("DELETE FROM courses WHERE id = ?");
                $stmt3->bind_param("i", $course_id);
                $stmt3->execute();
                $stmt3->close();

                // Commit the transaction
                $conn->commit();
                echo "Course and related content deleted successfully!";
            } catch (Exception $e) {
                // Rollback the transaction in case of any error
                $conn->rollback();
                echo "Error deleting course: " . $e->getMessage();
            }
        } else {
            echo "You are not authorized to delete this course.";
        }
    } else {
        echo "Access denied. Only instructors can delete their own courses.";
    }
}

$conn->close();
?>
