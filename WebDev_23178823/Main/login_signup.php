<?php
$signupMode = isset($_GET['mode']) && $_GET['mode'] === 'signup';
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

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action === 'signup') {
        // Signup Logic
        $fname = htmlspecialchars(trim($_POST['fname']));
        $lname = htmlspecialchars(trim($_POST['lname']));
        $email = htmlspecialchars(trim($_POST['email']));
        $password = htmlspecialchars(trim($_POST['password']));

        // Check if the email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            $message = "Email already exists. Please use a different email.";
        } else {

            $stmt = $pdo->prepare("SELECT MAX(id) AS max_id FROM users");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $newId = $result['max_id'] + 1;

            // Insert new user into the database
            $stmt = $pdo->prepare("INSERT INTO users (id, first_name, last_name, email, password, role) VALUES (:id, :fname, :lname, :email, :password, 'Student')");
            $stmt->execute([
                'id' => $newId,
                'fname' => $fname,
                'lname' => $lname,
                'email' => $email,
                'password' => $password,
                'role' => $role,
            ]);
            $message = "Signup successful! You can now log in.";
        }
    } elseif ($action === 'login') {
        // Login Logic
        $email = htmlspecialchars(trim($_POST['email']));
        $password = htmlspecialchars(trim($_POST['password']));

        // Check if the email exists 
        $stmt = $pdo->prepare("SELECT id, first_name, password, role FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            if ($password === $user['password']) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'Student') {
                    header("Location: student_dashboard.php");
                } elseif ($user['role'] === 'Instructor') {
                    header("Location: instructor_dashboard.php");
                } elseif ($user['role'] === 'Admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    $message = "Invalid role.";
                }
                exit;
            } else {
                $message = "Invalid password. Please try again.";
            }
        } else {
            $message = "No account found with that email. Please sign up.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Sign Up</title>
    <link href="/CSS/Login.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 200px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container" id="container">
        
        <!-- Sign Up Form (Left Side) -->
        <div class="form-container signup-container">
            <form action="" method="POST" class="p-4 w-100">
                <h2 class="mb-4">Create Account</h2>
                <input type="hidden" name="action" value="signup">
                <input type="text" name="fname" class="form-control mb-3" placeholder="First Name" required>
                <input type="text" name="lname" class="form-control mb-3" placeholder="Last Name" required>
                <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                <button type="submit" class="btn btn-primary w-100">Sign Up</button>
            </form>
        </div>

        <!-- Sign In Form (Right Side) -->
        <div class="form-container signin-container">
            <form action="" method="POST" class="p-4 w-100">
                <h2 class="mb-4">Welcome Back!</h2>
                <input type="hidden" name="action" value="login">
                <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                <button type="submit" class="btn btn-primary w-100">Sign In</button>
            </form>
        </div>

        <!-- Overlay Section -->
        <div class="overlay-container" id="overlay-container">
            <div class="overlay">
                <div class="logo">
                    <a href="Home.html">
                        <img src="/Images/LL.png" alt="LogicLaunch Logo" />
                    </a>
                </div>
                <h1 class="mb-4">Hello, Friend!</h1>
                <p>Enter your personal details and start your journey with us!</p>
                <button class="btn btn-light mt-4" id="toggle-btn">Switch to Sign In</button>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');
        const toggleBtn = document.getElementById('toggle-btn');

        toggleBtn.addEventListener('click', () => {
            container.classList.toggle('active');
            toggleBtn.textContent = container.classList.contains('active')
                ? 'Switch to Sign Up'
                : 'Switch to Sign In';
        });
    </script>
</body>

</html>