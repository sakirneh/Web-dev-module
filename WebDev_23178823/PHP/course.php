<?php
session_start();

// Database connection
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

// Get the course ID from the URL
if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    // Fetch course details along with the instructor's name
    $stmt = $pdo->prepare("
        SELECT courses.*, users.first_name AS instructor_first_name, users.last_name AS instructor_last_name
        FROM courses
        LEFT JOIN users ON courses.instructor_id = users.id
        WHERE courses.id = :id
    ");
    $stmt->execute(['id' => $course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        die("Course not found.");
    }


    // Fetch course content from the content table
    $content_stmt = $pdo->prepare("SELECT * FROM content WHERE course_id = :course_id");
    $content_stmt->execute(['course_id' => $course_id]);
    $content = $content_stmt->fetch(PDO::FETCH_ASSOC);

    $is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin';
    if (!$content || (!$is_admin && isset($content['is_approved']) && $content['is_approved'] == 0)) {
        // Show an alert and exit the page
        echo "<div class='modal' tabindex='-1' id='approvalModal' role='dialog'>
                <div class='modal-dialog' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title'>Course Pending Approval</h5>
                            <button type='button' class='close' data-bs-dismiss='modal' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button>
                        </div>
                        <div class='modal-body'>
                            <p>This course is awaiting approval and cannot be accessed yet.</p>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' id='goBackBtn'>OK</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.getElementById('goBackBtn').addEventListener('click', function() {
                    window.history.back();
                });
                var approvalModal = new bootstrap.Modal(document.getElementById('approvalModal'));
                approvalModal.show();
            </script>";
        if (!$is_admin) {
            exit; // Stop the rest of the page from loading for non-admin users
        }
    }


    // Fetch comments from the database with user details
    $comments_stmt = $pdo->prepare("SELECT comments.*, users.first_name, users.last_name FROM comments 
                                    LEFT JOIN users ON comments.user_id = users.id
                                    WHERE comments.course_id = :course_id AND comments.is_approved = 1 
                                    ORDER BY comments.created_at DESC");
    $comments_stmt->execute(['course_id' => $course_id]);
    $comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle new comment submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'])) {
        $user_id = $_SESSION['user_id'] ?? null;
        $comment_text = $_POST['comment_text'];

        if ($user_id && !empty($comment_text)) {
            $add_comment_stmt = $pdo->prepare("INSERT INTO comments (course_id, user_id, comment_text, is_approved, created_at) VALUES (:course_id, :user_id, :comment_text, 0, NOW())");
            $add_comment_stmt->execute([
                'course_id' => $course_id,
                'user_id' => $user_id,
                'comment_text' => $comment_text
            ]);
            // Redirect to the same page to prevent duplicate submission on reload
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $course_id);
            exit;
        } else {
            echo "<div class='alert alert-danger'>Please log in and enter a comment.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course: <?php echo htmlspecialchars($course['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/CSS/Course.css" rel="stylesheet">
    <style>
        .logo {
            text-align: center;
            background-color: #17a2b8;
            border-radius: 10px 10px 10px 10px;
            margin-top: 20px;
            margin-bottom: 20px;
        }


        .logo img {
            max-width: 200px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="/Images/LL.png" alt="LogicLaunch Logo" />
        </div>
        <h2>Course: <?php echo htmlspecialchars($course['title']); ?></h2>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($course['description']); ?></p>
        <p><strong>Genre:</strong> <?php echo htmlspecialchars($course['genre']); ?></p>
        <p><strong>Instructor:</strong>
            <?php echo htmlspecialchars($course['instructor_first_name'] . ' ' . $course['instructor_last_name']); ?>
        </p>
        <p><strong>Created at:</strong> <?php echo htmlspecialchars($course['created_at']); ?></p>

        <h3>Course Content:</h3>
        <p><?php echo htmlspecialchars($content['text_1']); ?></p>
        <p><?php echo htmlspecialchars($content['text_2']); ?></p>
        <p><?php echo htmlspecialchars($content['text_3']); ?></p>
        <p><?php echo htmlspecialchars($content['text_4']); ?></p>
        <p><?php echo htmlspecialchars($content['text_5']); ?></p>
        <p><?php echo htmlspecialchars($content['text_6']); ?></p>


        <?php if (!empty($content['video_url'])): ?>
            <h4>Watch the Video:</h4>
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item"
                    src="https://www.youtube.com/embed/<?php echo htmlspecialchars($content['video_url']); ?>"
                    allowfullscreen>
                </iframe>
            </div>
        <?php else: ?>
            <p>No video available for this course.</p>
        <?php endif; ?>

        <h3>Comments</h3>
        <form method="POST">
            <div class="mb-3">
                <label for="comment_text" class="form-label">Add a Comment:</label>
                <textarea class="form-control" id="comment_text" name="comment_text" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Comment</button>
        </form>

        <h4>Previous Comments:</h4>
        <?php if (!empty($comments)): ?>
            <ul class="list-group">
                <?php foreach ($comments as $comment): ?>
                    <li class="list-group-item">
                        <p><strong><?php echo htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']); ?>:</strong>
                            <?php echo htmlspecialchars($comment['comment_text']); ?></p>
                        <small>Posted on: <?php echo htmlspecialchars($comment['created_at']); ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>
    </div>
    </div>
</body>

</html>