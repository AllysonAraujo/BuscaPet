<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

// Get user stats
$database = new Database();
$db = $database->getConnection();

// Count user's pets
$query = "SELECT COUNT(*) as total FROM pets WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$pet_count = $stmt->fetch()['total'];

// Count user's favorites
$query = "SELECT COUNT(*) as total FROM favorites WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$favorite_count = $stmt->fetch()['total'];

// Get user's recent activity logs
$query = "SELECT action, details, created_at FROM logs WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 10";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$recent_activities = $stmt->fetchAll();

// Get user data
$query = "SELECT * FROM users WHERE id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - BuscaPet</title>
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
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item active" href="profile.php"><i class="fas fa-user-edit"></i> Perfil</a></li>
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
            <!-- User Profile Card -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                        </div>
                        <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                        <small class="text-muted">Membro desde <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></small>
                    </div>
                    
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stats-card bg-primary">
                                    <h3><?php echo $pet_count; ?></h3>
                                    <p class="mb-0">Pets Cadastrados</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stats-card bg-danger">
                                    <h3><?php echo $favorite_count; ?></h3>
                                    <p class="mb-0">Favoritos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-bolt"></i> Ações Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="add-pet.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Adicionar Pet
                            </a>
                            <a href="my-pets.php" class="btn btn-outline-primary">
                                <i class="fas fa-paw"></i> Ver Meus Pets
                            </a>
                            <a href="favorites.php" class="btn btn-outline-danger">
                                <i class="fas fa-heart"></i> Ver Favoritos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Update Profile Form -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-user-edit"></i> Atualizar Perfil</h5>
                    </div>
                    <div class="card-body">
                        <div id="alertContainer"></div>
                        
                        <form id="profileForm" class="needs-validation" novalidate>
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Nome de Usuário</label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?php echo htmlspecialchars($user['username']); ?>" 
                                               required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_]+">
                                        <div class="invalid-feedback">
                                            O usuário deve ter entre 3-50 caracteres (apenas letras, números e _).
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($user['email']); ?>" 
                                               required maxlength="100">
                                        <div class="invalid-feedback">
                                            Por favor, informe um email válido.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                            </div>
                        </form>
                        
                        <hr>
                        
                        <!-- Change Password Form -->
                        <h6><i class="fas fa-key"></i> Alterar Senha</h6>
                        
                        <form id="passwordForm" class="needs-validation" novalidate>
                            <input type="hidden" name="action" value="change_password">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Senha Atual</label>
                                        <input type="password" class="form-control" id="current_password" 
                                               name="current_password" required minlength="6">
                                        <div class="invalid-feedback">
                                            Por favor, informe sua senha atual.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Nova Senha</label>
                                        <input type="password" class="form-control" id="new_password" 
                                               name="new_password" required minlength="6">
                                        <div class="invalid-feedback">
                                            A nova senha deve ter pelo menos 6 caracteres.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key"></i> Alterar Senha
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Atividade Recente</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_activities)): ?>
                        <p class="text-muted">Nenhuma atividade recente.</p>
                        <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_activities as $activity): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">
                                        <?php
                                        $action_names = [
                                            'user_login' => 'Login realizado',
                                            'user_logout' => 'Logout realizado',
                                            'pet_created' => 'Pet cadastrado',
                                            'pet_updated' => 'Pet atualizado',
                                            'pet_deleted' => 'Pet removido',
                                            'pet_favorited' => 'Pet favoritado',
                                            'pet_unfavorited' => 'Pet desfavoritado'
                                        ];
                                        echo $action_names[$activity['action']] ?? $activity['action'];
                                        ?>
                                    </div>
                                    <small class="text-muted"><?php echo htmlspecialchars($activity['details']); ?></small>
                                </div>
                                <small class="text-muted">
                                    <?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?>
                                </small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
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
        // Profile update form handler
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!this.checkValidity()) {
                this.classList.add('was-validated');
                return;
            }
            
            const formData = new FormData(this);
            
            showLoading();
            
            fetch('api/profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    if (data.reload) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
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
        
        // Password change form handler
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!this.checkValidity()) {
                this.classList.add('was-validated');
                return;
            }
            
            const formData = new FormData(this);
            
            showLoading();
            
            fetch('api/profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    this.reset();
                    this.classList.remove('was-validated');
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