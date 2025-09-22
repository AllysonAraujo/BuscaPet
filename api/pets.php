<?php
header('Content-Type: application/json');
session_start();
require_once '../src/Pet.php';
require_once '../src/Favorite.php';

$method = $_SERVER['REQUEST_METHOD'];
$pet = new Pet();
$favorite = new Favorite();

// Handle GET requests (fetch pets)
if ($method === 'GET') {
    $search = $_GET['search'] ?? '';
    $species = $_GET['species'] ?? '';
    $page = max(1, intval($_GET['page'] ?? 1));
    $per_page = 12;
    $offset = ($page - 1) * $per_page;
    
    $pets = $pet->getAll($per_page, $offset, $search, $species);
    $total_pets = $pet->getCount($search, $species);
    $total_pages = ceil($total_pets / $per_page);
    
    // Check favorites for logged in users
    if (isset($_SESSION['user_id'])) {
        foreach ($pets as &$petItem) {
            $petItem['is_favorited'] = $favorite->isFavorited($_SESSION['user_id'], $petItem['id']);
        }
    }
    
    echo json_encode([
        'success' => true,
        'pets' => $pets,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_pets' => $total_pets,
            'per_page' => $per_page
        ]
    ]);
    exit();
}

// Handle POST requests (create/update/delete pets)
if ($method === 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Você precisa estar logado']);
        exit();
    }
    
    $action = $_POST['action'] ?? 'create';
    
    switch ($action) {
        case 'create':
            $name = trim($_POST['name'] ?? '');
            $species = trim($_POST['species'] ?? '');
            $breed = trim($_POST['breed'] ?? '');
            $age = !empty($_POST['age']) ? intval($_POST['age']) : null;
            $description = trim($_POST['description'] ?? '');
            $image_url = '';
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image_url = handleImageUpload($_FILES['image']);
                if (!$image_url) {
                    echo json_encode(['success' => false, 'message' => 'Erro no upload da imagem']);
                    exit();
                }
            }
            
            // Validation
            if (empty($name) || empty($species)) {
                echo json_encode(['success' => false, 'message' => 'Nome e espécie são obrigatórios']);
                exit();
            }
            
            $result = $pet->create($name, $species, $breed, $age, $description, $image_url, $_SESSION['user_id']);
            echo json_encode($result);
            break;
            
        case 'update':
            $id = intval($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $species = trim($_POST['species'] ?? '');
            $breed = trim($_POST['breed'] ?? '');
            $age = !empty($_POST['age']) ? intval($_POST['age']) : null;
            $description = trim($_POST['description'] ?? '');
            
            // Get current pet data
            $currentPet = $pet->getById($id);
            if (!$currentPet) {
                echo json_encode(['success' => false, 'message' => 'Pet não encontrado']);
                exit();
            }
            
            $image_url = $currentPet['image_url'];
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $new_image_url = handleImageUpload($_FILES['image']);
                if ($new_image_url) {
                    // Delete old image if exists
                    if ($image_url && file_exists("../" . $image_url)) {
                        unlink("../" . $image_url);
                    }
                    $image_url = $new_image_url;
                }
            }
            
            // Validation
            if (empty($name) || empty($species)) {
                echo json_encode(['success' => false, 'message' => 'Nome e espécie são obrigatórios']);
                exit();
            }
            
            $result = $pet->update($id, $name, $species, $breed, $age, $description, $image_url, $_SESSION['user_id']);
            echo json_encode($result);
            break;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID inválido']);
                exit();
            }
            
            // Get pet data to delete image
            $petData = $pet->getById($id);
            if ($petData && $petData['image_url'] && file_exists("../" . $petData['image_url'])) {
                unlink("../" . $petData['image_url']);
            }
            
            $result = $pet->delete($id, $_SESSION['user_id']);
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
            break;
    }
    exit();
}

// Handle other methods
echo json_encode(['success' => false, 'message' => 'Método não permitido']);

function handleImageUpload($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Check file type
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        return false;
    }
    
    // Create upload directory if it doesn't exist
    $upload_dir = '../assets/images/pets/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('pet_') . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return 'assets/images/pets/' . $filename;
    }
    
    return false;
}
?>