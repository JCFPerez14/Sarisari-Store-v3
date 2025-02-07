<?php
require_once 'db_to_php.php';
startSecureSession();
requireLogin();
$conn = connectDB();
// Add admin verification before any modification operations
    $isSuperAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'SuperAdmin';    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $isSuperAdmin) {
        if (isset($_POST['add_user'])) {
            // Validate username format
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
                die("Invalid username format");
            }
            
            // Use prepared statements
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->bind_param("ss", $username, $password_hash);
            if ($stmt->execute()) {
                $message = "User added successfully";
            }
            $stmt->close();
        }
    
    if (isset($_POST['update_user'])) {
        $user_id = (int)$_POST['user_id'];
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username='$username', password='$password' WHERE user_id=$user_id";
        } else {
            $sql = "UPDATE users SET username='$username' WHERE user_id=$user_id";
        }
        if ($conn->query($sql) === TRUE) {
            $message = "User updated successfully";
        }
    }
    
    if (isset($_POST['delete_user'])) {
        $user_id = (int)$_POST['user_id'];
        $sql = "DELETE FROM users WHERE user_id=$user_id AND username != 'SuperAdmin'";
        if ($conn->query($sql) === TRUE) {
            $message = "User deleted successfully";
        }
    }
}
?>

<!doctype html>
<html lang="en">
    <head>
    <title>User Management Dashboard</title>
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
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <style>
            :root {
                --primary-color: #2c3e50;
                --secondary-color: #34495e;
            }
            
            body {
                background-color: #f8f9fa;
                color: var(--primary-color);
            }
            
            .dashboard-header {
                background-color: var(--primary-color);
                color: white;
                padding: 1rem;
                margin-bottom: 2rem;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .card {
                border: none;
                border-radius: 10px;
                box-shadow: 0 0 15px rgba(0,0,0,0.1);
                margin-bottom: 2rem;
            }
            
            .card-header {
                background-color: var(--secondary-color);
                color: white;
                border-radius: 10px 10px 0 0 !important;
            }
            
            .form-control {
                border-radius: 5px;
                border: 1px solid #dee2e6;
                padding: 0.5rem;
            }
            
            .btn-custom {
                background-color: var(--primary-color);
                color: white;
                border-radius: 5px;
                padding: 0.5rem 1rem;
                transition: all 0.3s;
            }
            
            .btn-custom:hover {
                background-color: var(--secondary-color);
                transform: translateY(-2px);
            }
            
            .table {
                background-color: white;
                border-radius: 10px;
                overflow: hidden;
            }
            
            .table th {
                background-color: var(--secondary-color);
                color: white;
                border: none;
            }
            
            .message {
                padding: 1rem;
                border-radius: 5px;
                background-color: #d4edda;
                border-color: #c3e6cb;
                color: #155724;
                margin-bottom: 1rem;
            }
        </style>
    </head>

    <body>
        <header class="dashboard-header">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0"><i class="fas fa-users-cog me-2"></i>User Management Dashboard</h2>
                    <div class="d-flex align-items-center">
                    <a href="shop_interface.php" class="btn btn-custom ms-2">
                        <i class="fa fa-shopping-bag me-2"></i>Shop Interface
                    </a>
                    <form method="post" action="logout.php">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                    </div>
                </div>
            </div>
        </header>
            <main class="container">
            <?php if(isset($message)): ?>
                <div class="message">
                    <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add New User</h3>
                </div>
                <div class="card-body">
                    <form method="post" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" name="add_user" class="btn btn-custom w-100">
                                <i class="fas fa-plus me-2"></i>Add User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0"><i class="fas fa-user-list me-2"></i>User List</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>New Password</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT user_id, username FROM users";
                                $result = $conn->query($sql);
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<form method='post'>";
                                    echo "<input type='hidden' name='user_id' value='".(isset($row['user_id']) ? $row['user_id'] : '')."'>";
                                    echo "<td>".(isset($row['user_id']) ? $row['user_id'] : '')."</td>";
                                    if ($isSuperAdmin) {
                                        echo "<td><input type='text' name='username' value='".$row['username']."' class='form-control'></td>";
                                        echo "<td><input type='password' name='password' placeholder='Leave empty to keep current' class='form-control'></td>";
                                        echo "<td class='d-flex gap-2'>";
                                        echo "<button type='submit' name='update_user' class='btn btn-custom'><i class='fas fa-edit me-2'></i>Update</button>";
                                        if($row['username'] != 'SuperAdmin') {
                                            echo "<button type='submit' name='delete_user' class='btn btn-danger'><i class='fas fa-trash me-2'></i>Delete</button>";
                                        }
                                        echo "</td>";
                                    } else {
                                        echo "<td>".$row['username']."</td>";
                                        echo "<td>********</td>";
                                        echo "<td>No actions available</td>";
                                    }
                                    echo "</form>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php
            // Assuming you have the necessary session checks for Super Admin
            if ($_SERVER["REQUEST_METHOD"] == "POST" && $isSuperAdmin) {
                if (isset($_POST['enable_user'])) {
                    $username = mysqli_real_escape_string($conn, $_POST['username']);
                    mysqli_query($conn, "UPDATE users SET account_disabled = 0, failed_attempts = 0 WHERE username = '$username'");
                    $message = "User account enabled successfully.";
                }
            }

            // Fetch disabled users
            $disabled_users_query = "SELECT username FROM users WHERE account_disabled = 1";
            $disabled_users_result = $conn->query($disabled_users_query);
            ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Disabled User Accounts</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $disabled_users_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['username']; ?></td>
                                    <td>
                                        <form method="post">
                                            <input type="hidden" name="username" value="<?php echo $row['username']; ?>">
                                            <button type="submit" name="enable_user" class="btn btn-success">Enable</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
