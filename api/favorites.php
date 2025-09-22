<?php
header('Content-Type: application/json');
session_start();
require_once '../src/Favorite.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Você precisa estar logado']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$favorite = new Favorite();

// Handle GET requests (fetch favorites)
if ($method === 'GET') {
    $page = max(1, intval($_GET['page'] ?? 1));
    $per_page = 12;
    $offset = ($page - 1) * $per_page;
    
    $favorites = $favorite->getUserFavorites($_SESSION['user_id'], $per_page, $offset);
    
    echo json_encode([
        'success' => true,
        'favorites' => $favorites
    ]);
    exit();
}

// Handle POST requests (add/remove favorites)
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $action = $input['action'] ?? '';
    $pet_id = intval($input['pet_id'] ?? 0);
    
    if (empty($action) || $pet_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit();
    }
    
    switch ($action) {
        case 'add':
            $result = $favorite->add($_SESSION['user_id'], $pet_id);
            break;
            
        case 'remove':
            $result = $favorite->remove($_SESSION['user_id'], $pet_id);
            break;
            
        default:
            $result = ['success' => false, 'message' => 'Ação inválida'];
            break;
    }
    
    echo json_encode($result);
    exit();
}

// Handle other methods
echo json_encode(['success' => false, 'message' => 'Método não permitido']);
?>