<?php
session_start();
require_once '../config/database.php';

class Auth {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function register($username, $email, $password) {
        try {
            // Check if username or email already exists
            $query = "SELECT id FROM users WHERE username = :username OR email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Usuário ou email já existe'];
            }

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
                $this->logActivity(null, 'user_registered', "New user registered: $username");
                return ['success' => true, 'message' => 'Usuário cadastrado com sucesso'];
            }

        } catch (PDOException $exception) {
            return ['success' => false, 'message' => 'Erro ao cadastrar usuário: ' . $exception->getMessage()];
        }

        return ['success' => false, 'message' => 'Erro desconhecido'];
    }

    public function login($username, $password) {
        try {
            $query = "SELECT id, username, email, password FROM users WHERE username = :username OR email = :username";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    
                    $this->logActivity($user['id'], 'user_login', "User logged in: " . $user['username']);
                    return ['success' => true, 'message' => 'Login realizado com sucesso'];
                }
            }

            $this->logActivity(null, 'login_failed', "Failed login attempt: $username");
            return ['success' => false, 'message' => 'Credenciais inválidas'];

        } catch (PDOException $exception) {
            return ['success' => false, 'message' => 'Erro no login: ' . $exception->getMessage()];
        }
    }

    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logActivity($_SESSION['user_id'], 'user_logout', "User logged out: " . $_SESSION['username']);
        }
        
        session_destroy();
        return ['success' => true, 'message' => 'Logout realizado com sucesso'];
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

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

    private function logActivity($user_id, $action, $details) {
        try {
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $query = "INSERT INTO logs (user_id, action, details, ip_address) VALUES (:user_id, :action, :details, :ip_address)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':ip_address', $ip_address);
            $stmt->execute();
        } catch (PDOException $exception) {
            error_log("Failed to log activity: " . $exception->getMessage());
        }
    }
}
?>