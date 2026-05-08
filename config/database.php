<?php
/**
 * Database Configuration
 * Using PDO for secure database connections
 */

define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');      // Change if your MySQL user is different
define('DB_PASSWORD', '1234');          // Change if your MySQL has password
define('DB_NAME', 'hospital_db');

/**
 * Create database connection using PDO
 * @return PDO
 */
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Fetch associative arrays
            PDO::ATTR_EMULATE_PREPARES => false,                // Use real prepared statements
        ];
        
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
        return $pdo;
        
    } catch (PDOException $e) {
        die("Database Connection Failed: " . $e->getMessage());
    }
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect to login page if not authenticated
 */
function requireAuth() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Please login to access this page.";
        header("Location: /hospital-management/auth/login.php");
        exit();
    }
}

/**
 * Set success message in session
 */
function setSuccess($message) {
    $_SESSION['success'] = $message;
}

/**
 * Set error message in session
 */
function setError($message) {
    $_SESSION['error'] = $message;
}

/**
 * Display and clear flash messages
 */
function showFlashMessages() {
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success" id="success-alert">' . htmlspecialchars($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger" id="error-alert">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
}
?>