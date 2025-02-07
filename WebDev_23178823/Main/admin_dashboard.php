<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login-Signup.php");
    exit;
}


$host = 'localhost';
$db = 'logic launch';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);


if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Query to get the number of courses, instructors, and students
$query_courses = "SELECT COUNT(*) AS course_count FROM courses";
$query_instructors = "SELECT COUNT(*) AS instructor_count FROM users WHERE role = 'Instructor'";
$query_students = "SELECT COUNT(*) AS student_count FROM users WHERE role = 'Student'";
$query_comments = "SELECT COUNT(*) AS comments_count FROM comments";

// Execute queries
$result_courses = $conn->query($query_courses);
$result_instructors = $conn->query($query_instructors);
$result_students = $conn->query($query_students);
$result_comments = $conn->query($query_comments);

// Fetch the results
$course_count = $result_courses->fetch_assoc()['course_count'];
$instructor_count = $result_instructors->fetch_assoc()['instructor_count'];
$student_count = $result_students->fetch_assoc()['student_count'];
$comments_count = $result_comments->fetch_assoc()['comments_count'];

// Fetch user data based on the logged-in user ID
$user_id = $_SESSION['user_id'];
$user_query = $conn->query("SELECT first_name, last_name, email FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

// Fetch courses awaiting approval
$awaiting_courses_query = $conn->query("SELECT DISTINCT courses.id, courses.title, courses.genre FROM courses 
    INNER JOIN content ON courses.id = content.course_id 
    WHERE content.is_approved = 0");
$awaiting_courses = $awaiting_courses_query->fetch_all(MYSQLI_ASSOC);

// Fetch comments awaiting approval with course titles
$stmt = $conn->prepare("
    SELECT comments.id, comments.comment_text, comments.created_at, 
           users.first_name, users.last_name, courses.title AS course_title
    FROM comments
    LEFT JOIN users ON comments.user_id = users.id
    LEFT JOIN courses ON comments.course_id = courses.id
    WHERE comments.is_approved = 0
    ORDER BY comments.created_at DESC
");


$stmt->execute();
$result = $stmt->get_result();
$awaiting_comments = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/CSS/Dash.css" rel="stylesheet">
    <style>
        .logo {
            text-align: center;
            background-color: #17a2b8;
            padding: 20px;
            border-radius: 10px 10px 10px 10px;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 200px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">

            <!-- Toggle Sidebar Button -->
            <div class="toggle-sidebar" onclick="toggleSidebar()">☰ Menu</div>

            <nav class="col-md-3 col-lg-2 d-md-block sidebar py-4">
                <h4 class="text-center">Admin Dashboard</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('home')">🏠 Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('courses')">📚 Approval - Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('comments')">💬 Approval - Comments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('profile')">👤 Profile</a>
                    </li>
                    <li class="nav-item mt-auto">
                        <a class="nav-link text-danger" href="Home.html">🚪 Log Out</a>
                    </li>
                </ul>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div id="home" class="content" style="text-align: center">
                    <div class="logo">
                        <a href="Home.html">
                            <img src="/Images/LL.png" alt="LogicLaunch Logo" />
                        </a>
                    </div>
                    <p>Welcome to the General Statistics of Logic Launch:</p>
                    <div class="stats">
                        <div class="stat-item">
                            <h4>Total Courses</h4>
                            <p><?php echo $course_count; ?></p>
                        </div>
                        <div class="stat-item">
                            <h4>Total Instructors</h4>
                            <p><?php echo $instructor_count; ?></p>
                        </div>
                        <div class="stat-item">
                            <h4>Total Students</h4>
                            <p><?php echo $student_count; ?></p>
                        </div>
                        <div class="stat-item">
                            <h4>Total Comments</h4>
                            <p><?php echo $comments_count; ?></p>
                        </div>
                    </div>
                </div>

                <div id="courses" class="content" style="display: none;">
                    <h2>📚 Awaiting Approval - Courses</h2>
                    <ul class="list-group">
                        <?php foreach ($awaiting_courses as $course): ?>
                            <li class="list-group-item">
                                <a href="/PHP/course.php?id=<?php echo $course['id']; ?>">
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </a>
                                <button class="btn btn-success btn-sm float-end"
                                    onclick="approveCourse(<?php echo $course['id']; ?>)">Approve Course</button>
                                <button class="btn btn-danger btn-sm float-end me-2"
                                    onclick="dissapproveCourse(<?php echo $course['id']; ?>)">Disapprove Course</button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div id="comments" class="content" style="display: none;">
                    <h2>💬 Awaiting Approval - Comments</h2>
                    <ul class="list-group">
                        <?php foreach ($awaiting_comments as $comment): ?>
                            <li class="list-group-item">
                                <p>
                                    <strong>Course:</strong> <?php echo htmlspecialchars($comment['course_title']); ?><br>
                                    <strong>User:</strong>
                                    <?php echo htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']); ?><br>
                                    <strong>Comment:</strong> <?php echo htmlspecialchars($comment['comment_text']); ?><br>
                                </p>
                                <button class="btn btn-success btn-sm float-end"
                                    onclick="approveComment(<?php echo $comment['id']; ?>)">Approve Comment</button>
                                <button class="btn btn-danger btn-sm float-end me-2"
                                    onclick="dissapproveComment(<?php echo $comment['id']; ?>)">Disapprove Comment</button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div id="profile" class="content" style="display: none">
                    <h2>👤 Profile</h2>
                    <p><strong>Name:</strong>
                        <span
                            id="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                    </p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <h5>Settings</h5>
                    <ul>
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#changeNameModal">Change Name</a></li>
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</a>
                    </ul>
                </div>

                <!-- Change Name -->
                <div class="modal fade" id="changeNameModal" tabindex="-1" aria-labelledby="changeNameModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="changeNameForm">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="changeNameModalLabel">Change Name</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="first-name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first-name" name="first_name"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="last-name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last-name" name="last_name"
                                            required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="modal fade" id="changePasswordModal" tabindex="-1"
                    aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="changePasswordForm">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="current-password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current-password"
                                            name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new-password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new-password"
                                            name="new_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm-password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm-password"
                                            name="confirm_password" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function approveComment(commentId) {
            if (confirm("Are you sure you want to approve this comment?")) {
                fetch('/PHP/approve_comment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'comment_id=' + commentId
                })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        location.reload();
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        function dissapproveComment(commentId) {
            if (confirm("Are you sure you want to disapprove this comment?")) {
                fetch('/PHP/dissapprove_comment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + commentId
                })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        location.reload();
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>

    <script>
        function approveCourse(courseId) {
            if (confirm("Are you sure you want to approve this course?")) {
                fetch('/PHP/approve_course.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + courseId
                })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        location.reload();
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        function dissapproveCourse(courseId) {
            if (confirm("Are you sure you want to disapprove this course?")) {
                fetch('/PHP/dissapprove_course.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + courseId
                })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        location.reload();
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>

    <script>

        document.getElementById('changeNameForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('/PHP/change_name.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    const firstName = formData.get('first_name');
                    const lastName = formData.get('last_name');
                    document.getElementById('user-name').textContent = firstName + ' ' + lastName;
                    const changeNameModal = document.getElementById('changeNameModal');
                    const modalInstance = bootstrap.Modal.getInstance(changeNameModal);
                    modalInstance.hide();
                })
                .catch(error => console.error('Error:', error));
        });


        document.getElementById('changePasswordForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('/PHP/change_password.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    const changePasswordModal = document.getElementById('changePasswordModal');
                    const modalInstance = bootstrap.Modal.getInstance(changePasswordModal);
                    modalInstance.hide();
                })
                .catch(error => console.error('Error:', error));
        });
    </script>

    <script>
        // Sidebar Toggle Functionality
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const content = document.querySelector('.content');
            const menuButton = document.querySelector('.toggle-sidebar');

            // Toggle the sidebar visibility
            sidebar.classList.toggle('show');

            // Shift the content accordingly
            content.classList.toggle('shifted');

            // Hide the menu button when the sidebar is open
            if (sidebar.classList.contains('show')) {
                menuButton.classList.add('hide'); // Hide menu button
            } else {
                menuButton.classList.remove('hide'); // Show menu button
            }
        }

    </script>

    <script>
        function showSection(sectionId) {
            document.querySelectorAll('.content').forEach(section => {
                section.style.display = 'none';
            });
            document.getElementById(sectionId).style.display = 'block';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>