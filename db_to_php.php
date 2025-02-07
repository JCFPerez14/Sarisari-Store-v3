<?php
// Database connection
function connectDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "shop_db";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Add this function to check admin role
function checkRole($conn, $username, $roleToCheck) {
    $sql = "SELECT role FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['role'] === $roleToCheck;
    }
    
    return false;
}

function isAdmin($conn, $username) {
    return checkRole($conn, $username, 'admin');
}

function isSuperAdmin($conn, $username) {
    return checkRole($conn, $username, 'SuperAdmin');
}
// Session management
function startSecureSession() {
    session_start();
    if(isset($_SESSION['username'])) {
        $conn = connectDB();
        if(isSuperAdmin($conn, $_SESSION['username'])) {
            $_SESSION['role'] = 'SuperAdmin';
        } else if(isAdmin($conn, $_SESSION['username'])) {
            $_SESSION['role'] = 'admin';
        } else {
            $_SESSION['role'] = 'user';
        }
    }
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
}

// Admin authentication
function requireAdmin() {
    if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
        header("Location: SSSSJC.php");
        exit();
    }
}

// User authentication
function requireLogin() {
    if (!isset($_SESSION['username'])) {
        header("Location: SSSSJC.php");
        exit();
    }
}

// Database operations
function executeQuery($conn, $sql) {
    $result = $conn->query($sql);
    return $result;
}

// Sanitize input
function sanitizeInput($conn, $input) {
    return mysqli_real_escape_string($conn, $input);
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
    return true;
}
