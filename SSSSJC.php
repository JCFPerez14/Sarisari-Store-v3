<?php
session_start();
// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (isset($_SESSION['username'])) {
    header("Location: shop_interface.php");
    exit();
}

require_once 'config/database.php';

try {
    $conn = connectDB();
    
    if (isset($_POST['login'])) {
        // Validate and sanitize inputs
        if (!isset($_POST['username']) || !isset($_POST['password']) || 
            empty(trim($_POST['username'])) || empty(trim($_POST['password']))) {
            throw new Exception("Invalid input parameters");
        }
        
        $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
        $password = trim($_POST['password']);
        
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        if (!$stmt) {
            throw new Exception("Database prepare failed");
        }
        
        $stmt->bind_param("s", $username);
        if (!$stmt->execute()) {
            throw new Exception("Database query failed");
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if ($user['account_disabled']) {
                $error = "Your account is disabled. Please contact a Super Admin.";
            } else if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: shop_interface.php");
                exit();
            } else {
                $error = "Invalid username or password";
            }
        } else {
            $error = "Invalid username or password";
        }
        
        $stmt->close();
    }
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    header("Location: /Sarisari-Store-v3/error.php");
    exit();
}
?>
<!doctype html>
<html lang="en">
    <head>
        <title>Login Page</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        
        <!-- Local Bootstrap CSS -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 20px;
                background-image: url('assets/images/background1.jpg');
                background-size: cover;
                background-position: center;
            }

            .login-container {
                max-width: 400px;
                margin: 100px auto;
                background: rgba(255, 255, 255, 0.4); /* White background with transparency */
                backdrop-filter: blur(10px); /* Blur effect */
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }

            input[type="text"], input[type="password"] {
                width: 100%;
                padding: 10px;
                margin: 10px 0;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            input[type="submit"] {
                background: #4CAF50;
                color: white;
                padding: 10px 15px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                width: 100%;
            }
            .error {
                color: red;
                margin-bottom: 10px;
            }
            

        </style>
    </head>

    <body>
        <header>
            <!-- place navbar here -->
        </header>
        <main>
            <div class="login-container">
            <h2>Login</h2>
            <?php if(isset($error)) { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>
            <form method="POST" action="">
                <input type="text" name="username" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <input type="submit" name="login" value="Login">
            </form>
            </div>
            
        </main>
        <footer>
            <!-- place footer here -->
        </footer>
        <!-- Local JavaScript files -->
        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script>
            window.history.pushState(null, null, window.location.href);
            window.onpopstate = function () {
                window.history.pushState(null, null, window.location.href);
            };
        </script>
    </body>
</html>