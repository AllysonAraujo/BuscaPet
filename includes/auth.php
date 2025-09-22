<?php
/**
 * Authentication System
 * Handle user login, logout, and session management
 */

session_start();

require_once __DIR__ . '/../config/database.php';

class Auth {
    private $database;
    private $connection;
    
    public function __construct() {
        $this->database = new Database();
        $this->connection = $this->database->getConnection();
    }
    
    /**
     * User login
     */
    public function login($username, $password) {
        $stmt = $this->connection->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            
            return true;
        }
        
        return false;
    }
    
    /**
     * User logout
     */
    public function logout() {
        session_destroy();
        header("Location: /login.php");
        exit();
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Require login (redirect if not logged in)
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header("Location: /login.php");
            exit();
        }
    }
    
    /**
     * Get current user info
     */
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email']
            ];
        }
        return null;
    }
    
    /**
     * Register new user
     */
    public function register($username, $email, $password) {
        // Check if username or email already exists
        $stmt = $this->connection->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetchColumn() > 0) {
            return false; // User already exists
        }
        
        // Insert new user
        $stmt = $this->connection->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        return $stmt->execute([$username, $email, $hashedPassword]);
    }
    
    /**
     * Change password
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        // Verify current password
        $stmt = $this->connection->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return false;
        }
        
        // Update password
        $stmt = $this->connection->prepare("UPDATE users SET password = ? WHERE id = ?");
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        return $stmt->execute([$hashedPassword, $userId]);
    }
}

// Helper functions
function redirectIfLoggedIn() {
    $auth = new Auth();
    if ($auth->isLoggedIn()) {
        header("Location: /admin/dashboard.php");
        exit();
    }
}

function getCurrentUser() {
    $auth = new Auth();
    return $auth->getCurrentUser();
}

function requireLogin() {
    $auth = new Auth();
    $auth->requireLogin();
}

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $auth = new Auth();
    $auth->logout();
}
?>