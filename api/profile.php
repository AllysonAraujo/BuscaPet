<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Você precisa estar logado']);
    exit();
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

$data = $_POST;
$action = $data['action'] ?? '';

if (empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Ação não especificada']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

switch ($action) {
    case 'update_profile':
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');

        // Validation
        if (empty($username) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
            exit();
        }

        if (strlen($username) < 3 || strlen($username) > 50) {
            echo json_encode(['success' => false, 'message' => 'O nome de usuário deve ter entre 3 e 50 caracteres']);
            exit();
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            echo json_encode(['success' => false, 'message' => 'O nome de usuário deve conter apenas letras, números e underscore']);
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Email inválido']);
            exit();
        }

        try {
            // Check if username or email already exists (excluding current user)
            $query = "SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'Usuário ou email já está em uso por outra conta']);
                exit();
            }

            // Update user
            $query = "UPDATE users SET username = :username, email = :email WHERE id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);

            if ($stmt->execute()) {
                // Update session data
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                
                // Log activity
                logActivity($db, $_SESSION['user_id'], 'profile_updated', "Profile updated: $username");
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Perfil atualizado com sucesso',
                    'reload' => true
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar perfil']);
            }

        } catch (PDOException $exception) {
            echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $exception->getMessage()]);
        }
        break;

    case 'change_password':
        $current_password = $data['current_password'] ?? '';
        $new_password = $data['new_password'] ?? '';

        // Validation
        if (empty($current_password) || empty($new_password)) {
            echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
            exit();
        }

        if (strlen($new_password) < 6) {
            echo json_encode(['success' => false, 'message' => 'A nova senha deve ter pelo menos 6 caracteres']);
            exit();
        }

        try {
            // Get current password hash
            $query = "SELECT password FROM users WHERE id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            $user = $stmt->fetch();

            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
                exit();
            }

            // Verify current password
            if (!password_verify($current_password, $user['password'])) {
                echo json_encode(['success' => false, 'message' => 'Senha atual incorreta']);
                exit();
            }

            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password
            $query = "UPDATE users SET password = :password WHERE id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);

            if ($stmt->execute()) {
                // Log activity
                logActivity($db, $_SESSION['user_id'], 'password_changed', "Password changed for user: " . $_SESSION['username']);
                
                echo json_encode(['success' => true, 'message' => 'Senha alterada com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao alterar senha']);
            }

        } catch (PDOException $exception) {
            echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $exception->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
        break;
}

function logActivity($db, $user_id, $action, $details) {
    try {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $query = "INSERT INTO logs (user_id, action, details, ip_address) VALUES (:user_id, :action, :details, :ip_address)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':ip_address', $ip_address);
        $stmt->execute();
    } catch (PDOException $exception) {
        error_log("Failed to log activity: " . $exception->getMessage());
    }
}
?>