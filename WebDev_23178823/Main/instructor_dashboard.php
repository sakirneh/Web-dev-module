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

// Fetch user data based on the logged-in user ID
$user_id = $_SESSION['user_id'];
$user_query = $conn->query("SELECT first_name, last_name, email FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $genre = $_POST['genre'];
    $full_text = $_POST['full_text'];
    $video_url = $_POST['video_url'] ?? null;

    // Split full_text into sections 
    $text_parts = preg_split('/\n\s*\n/', $full_text);

    // Prepare individual text variables
    $text_1 = $text_parts[0] ?? "";
    $text_2 = $text_parts[1] ?? "";
    $text_3 = $text_parts[2] ?? "";
    $text_4 = $text_parts[3] ?? "";
    $text_5 = $text_parts[4] ?? "";
    $text_6 = $text_parts[5] ?? "";

    // Insert into courses table
    $course_stmt = $conn->prepare("
        INSERT INTO courses (title, description, instructor_id, genre, created_at) 
        VALUES (?, ?, ?, ?, NOW())
    ");
    $course_stmt->bind_param("ssis", $title, $description, $user_id, $genre);
    $course_stmt->execute();

    // Get the last inserted course ID
    $course_id = $conn->insert_id;

    // Insert into content table
    $content_stmt = $conn->prepare("
        INSERT INTO content (course_id, text_1, text_2, text_3, text_4, text_5, text_6, video_url, is_approved, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())
    ");
    $content_stmt->bind_param(
        "isssssss",
        $course_id,
        $text_1,
        $text_2,
        $text_3,
        $text_4,
        $text_5,
        $text_6,
        $video_url
    );
    $content_stmt->execute();

    echo "<div class='alert alert-success'>Course created successfully and awaiting approval!</div>";

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// Fetch courses created by the logged-in instructor
$courses_query = $conn->query("SELECT id, title, genre, created_at FROM courses WHERE instructor_id = $user_id");
$created_courses = $courses_query->fetch_all(MYSQLI_ASSOC);
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
                <h4 class="text-center">Instructor Dashboard</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('home')">🏠 Create Course</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('courses')">📚 Courses Created</a>
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
                    <h2>➕ Create New Course</h2>
                    <p>Welcome to your dashboard. Create new courses here.</p>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="course_title" class="form-label">Course Title</label>
                            <input type="text" class="form-control" id="course_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="course_description" class="form-label">Course Description</label>
                            <textarea class="form-control" id="course_description" name="description" rows="4"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="course_genre" class="form-label">Course Genre</label>
                            <input type="text" class="form-control" id="course_genre" name="genre" required>
                        </div>
                        <div class="mb-3">
                            <label for="course-text" class="form-label">Text </label>
                            <textarea class="form-control" id="course-text" name="full_text" rows="10"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="video-url" class="form-label">Video URL</label>
                            <input type="url" class="form-control" id="video-url" name="video_url">
                        </div>
                        <button type="submit" class="btn btn-primary">Create Course</button>
                    </form>
                </div>

                <!-- Courses Section -->
                <div id="courses" class="content" style="display: none;">
                    <h2>📚 Courses Created</h2>
                    <ul class="list-group">
                        <?php foreach ($created_courses as $course): ?>
                            <li class="list-group-item">
                                <h5>
                                    <a href="/PHP/course.php?id=<?php echo htmlspecialchars($course['id']); ?>"
                                        class="text-decoration-none">
                                        <?php echo htmlspecialchars($course['title']); ?>
                                    </a>
                                </h5>
                                <p><strong>Genre:</strong> <?php echo htmlspecialchars($course['genre']); ?></p>
                                <p><strong>Created on:</strong> <?php echo htmlspecialchars($course['created_at']); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>


                <div id="profile" class="content" style="display: none;">
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
                        </li>
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