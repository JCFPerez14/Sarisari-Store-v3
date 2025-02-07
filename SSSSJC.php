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
} catch (Exception $e) {
    error_log($e->getMessage());
    header("Location: /Sarisari-Store-v3/error.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    $query = "SELECT * FROM users WHERE BINARY username='$username'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Check if account is disabled
        if ($user['account_disabled']) {
            $error = "Your account is disabled. Please contact a Super Admin.";
        } else {
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $user['role']; 
            
                // Redirect based on role
                if ($_SESSION['role'] === 'SuperAdmin') {
                    header("Location: admin.php");
                } else {
                    header("Location: shop_interface.php");
                }
                exit();
            } else {
                // Increment failed attempts
                $failed_attempts = $user['failed_attempts'] + 1;
                $last_failed_attempt = date("Y-m-d H:i:s");
                
                mysqli_query($conn, "UPDATE users SET failed_attempts = $failed_attempts, last_failed_attempt = '$last_failed_attempt' WHERE username = '$username'");
                
                // Check if failed attempts exceed limit
                if ($failed_attempts >= 5) {
                    mysqli_query($conn, "UPDATE users SET account_disabled = 1 WHERE username = '$username'");
                    $error = "Your account has been disabled due to too many failed login attempts. Please contact a Super Admin.";
                } else {
                    $error = "Invalid username or password. Attempt $failed_attempts of 5.";
                }
            }
        }
    } else {
        $error = "Invalid username or password.";
    }}
?>
<!doctype html>
<html lang="en">
    <head>
    <title>Login Page</title>
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
            .signup-link {
                margin-top: 20px;
    	    }
    	    .signup-link a {
    	        text-decoration: none;
    	        padding: 8px 15px;
    	    }

        </style>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <!-- Bootstrap CSS v5.2.1 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
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
            <div class="signup-link" style="text-align: center; margin-top: 15px;">
                <p>Don't have an account? <a href="signup.php" class="btn btn-secondary">Sign Up</a></p>
            </div>
        </main>
        <footer>
            <!-- place footer here -->
        </footer>
        <!-- Bootstrap JavaScript Libraries -->
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
        <script>
            window.history.pushState(null, null, window.location.href);
            window.onpopstate = function () {
                window.history.pushState(null, null, window.location.href);
            };
        </script>
    </body>
</html>