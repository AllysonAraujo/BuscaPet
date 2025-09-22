<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'src/Pet.php';

$pet_model = new Pet();
$pet_id = intval($_GET['id'] ?? 0);

if ($pet_id <= 0) {
    header('Location: my-pets.php');
    exit();
}

// Get pet data
$pet = $pet_model->getById($pet_id);

// Check if pet exists and user owns it
if (!$pet || $pet['user_id'] != $_SESSION['user_id']) {
    header('Location: my-pets.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar <?php echo htmlspecialchars($pet['name']); ?> - BuscaPet</title>
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
                        <a class="nav-link" href="my-pets.php">Meus Pets</a>
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-edit"></i> Editar Pet: <?php echo htmlspecialchars($pet['name']); ?></h3>
                        <p class="mb-0 text-muted">Atualize as informações do seu pet</p>
                    </div>
                    
                    <div class="card-body">
                        <div id="alertContainer"></div>
                        
                        <!-- Current image -->
                        <?php if ($pet['image_url']): ?>
                        <div class="mb-3 text-center">
                            <h6>Imagem Atual:</h6>
                            <img src="<?php echo $pet['image_url']; ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" 
                                 class="image-preview" onerror="this.src='assets/images/default-pet.svg'">
                        </div>
                        <?php endif; ?>
                        
                        <form id="petForm" class="needs-validation" novalidate enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?php echo $pet['id']; ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nome do Pet *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               placeholder="Nome do pet" required maxlength="100"
                                               value="<?php echo htmlspecialchars($pet['name']); ?>">
                                        <div class="invalid-feedback">
                                            Por favor, informe o nome do pet.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="species" class="form-label">Espécie *</label>
                                        <select class="form-select" id="species" name="species" required>
                                            <option value="">Selecione a espécie</option>
                                            <option value="Dog" <?php echo $pet['species'] === 'Dog' ? 'selected' : ''; ?>>Cão</option>
                                            <option value="Cat" <?php echo $pet['species'] === 'Cat' ? 'selected' : ''; ?>>Gato</option>
                                            <option value="Bird" <?php echo $pet['species'] === 'Bird' ? 'selected' : ''; ?>>Pássaro</option>
                                            <option value="Rabbit" <?php echo $pet['species'] === 'Rabbit' ? 'selected' : ''; ?>>Coelho</option>
                                            <option value="Hamster" <?php echo $pet['species'] === 'Hamster' ? 'selected' : ''; ?>>Hamster</option>
                                            <option value="Fish" <?php echo $pet['species'] === 'Fish' ? 'selected' : ''; ?>>Peixe</option>
                                            <option value="Other" <?php echo $pet['species'] === 'Other' ? 'selected' : ''; ?>>Outros</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Por favor, selecione a espécie.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="breed" class="form-label">Raça</label>
                                        <input type="text" class="form-control" id="breed" name="breed" 
                                               placeholder="Raça (opcional)" maxlength="100"
                                               value="<?php echo htmlspecialchars($pet['breed']); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="age" class="form-label">Idade</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="age" name="age" 
                                                   placeholder="Idade" min="0" max="30"
                                                   value="<?php echo $pet['age']; ?>">
                                            <span class="input-group-text">anos</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Descrição</label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Descreva o temperamento, características especiais, cuidados necessários..."><?php echo htmlspecialchars($pet['description']); ?></textarea>
                                <div class="form-text">
                                    Uma boa descrição ajuda as pessoas a conhecer melhor o pet.
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="image" class="form-label">Nova Foto do Pet</label>
                                <input type="file" class="form-control" id="image" name="image" 
                                       accept="image/jpeg,image/png,image/gif,image/webp">
                                <div class="form-text">
                                    Deixe em branco para manter a foto atual. Formatos aceitos: JPG, PNG, GIF, WebP. Tamanho máximo: 5MB.
                                </div>
                            </div>
                            
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                                <a href="my-pets.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
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
        // Override the form submission to handle file upload correctly
        document.getElementById('petForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!this.checkValidity()) {
                this.classList.add('was-validated');
                return;
            }
            
            const formData = new FormData(this);
            
            showLoading();
            
            fetch('api/pets.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = 'my-pets.php';
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
            });
        });
    </script>
</body>
</html>