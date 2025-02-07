<?php
require_once 'db_to_php.php';
startSecureSession();
$conn = connectDB();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    if (!is_string($username) || strlen($username) < 3 || strlen($username) > 20) {
        die("Invalid username parameter");
    }
    
    if (!is_string($_POST['password']) || !is_string($_POST['confirm_password'])) {
        die("Invalid password parameter");
    }
    
    // Additional password strength validation
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $_POST['password'])) {
        $error = "Password must contain at least one uppercase letter, one lowercase letter, and one number";
    } else {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate input
        if (empty($username) || empty($password) || empty($confirm_password)) {
            $error = "All fields are required";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match";
        } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
            $error = "Username can only contain letters, numbers, and underscores.";
        } elseif (!preg_match("/[\W_]/", $password)) {
            $error = "Password must contain at least one special character.";
        } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username already exists";
        } else {
            // Hash password and insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            
            if ($stmt->execute()) {
                $_SESSION['message'] = "Registration successful! Please login.";
                header("Location: SSSSJC.php");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
  }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Sign Up - Shop Management System</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 20px;
                background-image: url('http://localhost/Sarisari-Store-main-main/database/background1.jpg');
                background-size: cover;
                background-position: center;
            }

            .card{
                margin: 100px auto;
                background: rgba(255, 255, 255, 0.4); /* White background with transparency */
                backdrop-filter: blur(10px); /* Blur effect */
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
    </style>
</head>
<body>
    <main>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body p-5">
                            <h2 class="text-center mb-4">Sign Up</h2>

                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username:</label>
                                    <input type="text" class="form-control" name="username" id="username" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password:</label>
                                    <input type="password" class="form-control" name="password" id="password" required>
                                    <small class="form-text text-muted">
                                        Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character (e.g., !, @, #, $).
                                    </small>
                                </div>

                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">Confirm Password:</label>
                                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">Sign Up</button>
                                </div>
                            </form>
                            
                            <p class="text-center mt-4">
                                Already have an account? <a href="SSSSJC.php" class="text-decoration-none">Login here</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
                        