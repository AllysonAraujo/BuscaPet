<?php
header('Content-Type: application/json');
require_once '../src/Auth.php';

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

// Handle logout via GET request
if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    $auth = new Auth();
    $result = $auth->logout();
    
    if ($result['success']) {
        header('Location: ../index.php');
        exit();
    } else {
        echo json_encode($result);
        exit();
    }
}

// Handle POST requests
if ($method !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

// Get POST data
$data = $_POST;
$action = $data['action'] ?? '';

if (empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Ação não especificada']);
    exit();
}

$auth = new Auth();

switch ($action) {
    case 'login':
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
            exit();
        }

        $result = $auth->login($username, $password);
        
        if ($result['success']) {
            $result['redirect'] = '../index.php';
        }
        
        echo json_encode($result);
        break;

    case 'register':
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $confirm_password = $data['confirm_password'] ?? '';

        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
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

        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'A senha deve ter pelo menos 6 caracteres']);
            exit();
        }

        if ($password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'As senhas não coincidem']);
            exit();
        }

        $result = $auth->register($username, $email, $password);
        
        if ($result['success']) {
            $result['redirect'] = '../login.php';
        }
        
        echo json_encode($result);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
        break;
}
?>