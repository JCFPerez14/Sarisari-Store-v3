<?php
require_once 'db_to_php.php';
startSecureSession();
requireLogin();
$conn = connectDB();
$isAdmin = isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'SuperAdmin');
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $isAdmin) {
        if (isset($_POST['submit'])) {
            // Sanitize and validate inputs
            $item = filter_var(trim($_POST['item']), FILTER_SANITIZE_STRING);
            $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
            $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
            
            if($price === false || $quantity === false || empty($item)) {
                die("Invalid input parameters");
            }
            
            $datemodified = date('Y-m-d H:i:s');
            
            // Use prepared statements
            $sql = $conn->prepare("INSERT INTO items (item_name, price, quantity, date_modified) VALUES (?, ?, ?, ?)");
            $sql->bind_param("sdis", $item, $price, $quantity, $datemodified);

            if ($sql->execute()) {
                echo "<p id='successMessage'>New item added successfully</p>";
                echo "<script>
                    setTimeout(function() {
                        document.getElementById('successMessage').style.display = 'none';
                    }, 3000);
                </script>";
            }
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && $isAdmin) {
        
        // Delete operation
        if (isset($_POST['delete'])) {
            $id = $_POST['id'];
            $sql = "DELETE FROM items WHERE id=$id";
        
            if ($conn->query($sql) === TRUE) {
                echo "<p>Record deleted successfully</p>";
            }
        }
    
        // Update operation
        if (isset($_POST['update'])) {
            $id = $_POST['id'];
            $item = $_POST['item'];
            $price = $_POST['price'];
            $quantity = $_POST['quantity'];
            $datemodified = date('Y-m-d H:i:s');
        
            $sql = "UPDATE items SET 
                    item_name='$item', 
                    price=$price, 
                    quantity=$quantity, 
                    date_modified='$datemodified' 
                    WHERE id=$id";
        
            if ($conn->query($sql) === TRUE) {
                echo "<p>Record updated successfully</p>";
            }
        }
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <title>Shop Item CRUD</title>
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

            .btn-customs {
                color: white !important;
            }
            .btn-custom:hover {
                background-color: var(--secondary-color);
                transform: translateY(-2px);
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    </head>

    <body>
        <header class="dashboard-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0"><i class="fas fa-store me-2"></i>Welcome, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?></h2>

                <div class="d-flex align-items-center">
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'SuperAdmin'): ?>
                        <a href="admin.php" class="btn btn-customs ms-2">
                            <i class="fas fa-users-cog me-2"></i>User Management
                        </a>
                    <?php endif; ?>

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
                    <h3 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Item</h3>
                </div>
                <div class="card-body">
                    <form method="post" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" name="item" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" name="submit" class="btn btn-custom w-100">
                                <i class="fas fa-plus me-2"></i>Add Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0"><i class="fas fa-list me-2"></i>Item List</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Date Modified</th>
                            <th>Actions</th>
                        </tr>
                        <?php
                        $sql = "SELECT * FROM items";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
                                echo "<input type='hidden' name='id' value='".$row['id']."'>";
                                echo "<td>".$row['id']."</td>";

                                if ($isAdmin) {
                                    echo "<td><input type='text' name='item' value='".$row['item_name']."'></td>";
                                    echo "<td><input type='number' step='0.01' name='price' value='".$row['price']."'></td>";
                                    echo "<td><input type='number' name='quantity' value='".$row['quantity']."'></td>";
                                    echo "<td>".$row['date_modified']."</td>";
                                        echo "<td class='d-flex gap-2'>
                                          <button type='submit' name='update' class='btn btn-custom'><i class='fas fa-edit me-2'></i>Update</button>
                                          <button type='submit' name='delete' class='btn btn-danger'>
                                              <i class='fas fa-trash me-2'></i>Delete
                                          </button>
                                        </td>";
                                } else {
                                    echo "<td>".$row['item_name']."</td>";
                                    echo "<td>".$row['price']."</td>";
                                    echo "<td>".$row['quantity']."</td>";
                                    echo "<td>".$row['date_modified']."</td>";
                                    echo "<td></td>";
                                }
                                echo "</form>";
                                echo "</tr>";
                            }
                        }
                        ?>
                        </table>
                    </div>
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
        <script>
            window.history.pushState(null, null, window.location.href);
            window.onpopstate = function () {
                window.history.pushState(null, null, window.location.href);
            };
        </script>
    </body>
</html>