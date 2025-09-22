<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

// Get user's pets
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM pets WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$my_pets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pets - BuscaPet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-paw"></i> BuscaPet
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="favorites.php">Meus Favoritos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="my-pets.php">Meus Pets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add-pet.php">Adicionar Pet</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-edit"></i> Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="api/auth.php?action=logout"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-paw"></i> Meus Pets</h2>
                    <div>
                        <span class="badge bg-primary fs-6 me-2"><?php echo count($my_pets); ?> pets cadastrados</span>
                        <a href="add-pet.php" class="btn btn-success">
                            <i class="fas fa-plus"></i> Adicionar Pet
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="alertContainer"></div>
        
        <div class="row">
            <?php if (empty($my_pets)): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-paw fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Você ainda não cadastrou nenhum pet</h4>
                <p class="text-muted">Que tal começar adicionando seu primeiro pet?</p>
                <a href="add-pet.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Adicionar Primeiro Pet
                </a>
            </div>
            <?php else: ?>
            <?php foreach ($my_pets as $pet): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card pet-card">
                    <div class="position-relative">
                        <img src="<?php echo $pet['image_url'] ?: 'assets/images/default-pet.svg'; ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($pet['name']); ?>"
                             onerror="this.src='assets/images/default-pet.svg'">
                        <div class="position-absolute top-0 end-0 m-2">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="edit-pet.php?id=<?php echo $pet['id']; ?>">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    </li>
                                    <li>
                                        <button class="dropdown-item text-danger" 
                                                onclick="deletePet(<?php echo $pet['id']; ?>, '<?php echo htmlspecialchars($pet['name'], ENT_QUOTES); ?>')">
                                            <i class="fas fa-trash"></i> Excluir
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></h5>
                        <span class="pet-species"><?php echo htmlspecialchars($pet['species']); ?></span>
                        <p class="pet-breed"><?php echo htmlspecialchars($pet['breed'] ?: 'Raça não informada'); ?></p>
                        <p class="pet-age">Idade: <?php echo $pet['age'] ? htmlspecialchars($pet['age']) . ' anos' : 'Não informada'; ?></p>
                        <p class="card-text"><?php echo htmlspecialchars(substr($pet['description'] ?: 'Sem descrição', 0, 100)); ?><?php echo strlen($pet['description']) > 100 ? '...' : ''; ?></p>
                        <small class="text-muted">Cadastrado em: <?php echo date('d/m/Y', strtotime($pet['created_at'])); ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o pet <strong id="petNameToDelete"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Excluir</button>
                </div>
            </div>
        </div>
    </div>

    <div id="loading" class="loading">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        let petIdToDelete = null;
        
        function deletePet(petId, petName) {
            petIdToDelete = petId;
            document.getElementById('petNameToDelete').textContent = petName;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
        
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (!petIdToDelete) return;
            
            showLoading();
            
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', petIdToDelete);
            
            fetch('api/pets.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    // Reload page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showMessage(data.message, 'danger');
                }
            })
            .catch(error => {
                showMessage('Erro na conexão: ' + error.message, 'danger');
            })
            .finally(() => {
                hideLoading();
                // Hide modal
                const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                deleteModal.hide();
                petIdToDelete = null;
            });
        });
    </script>
</body>
</html>