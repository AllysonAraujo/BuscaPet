<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'src/Favorite.php';

$favorite = new Favorite();
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 12;
$offset = ($page - 1) * $per_page;

$favorites = $favorite->getUserFavorites($_SESSION['user_id'], $per_page, $offset);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Favoritos - BuscaPet</title>
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
                        <a class="nav-link active" href="favorites.php">Meus Favoritos</a>
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
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-heart text-danger"></i> Meus Favoritos</h2>
                    <span class="badge bg-primary fs-6"><?php echo count($favorites); ?> favoritos</span>
                </div>
            </div>
        </div>

        <div id="alertContainer"></div>
        
        <div class="row">
            <?php if (empty($favorites)): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-heart-broken fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Você ainda não tem favoritos</h4>
                <p class="text-muted">Explore nossos pets e adicione seus favoritos clicando no coração!</p>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-search"></i> Explorar Pets
                </a>
            </div>
            <?php else: ?>
            <?php foreach ($favorites as $petItem): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card pet-card">
                    <div class="position-relative">
                        <img src="<?php echo $petItem['image_url'] ?: 'assets/images/default-pet.svg'; ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($petItem['name']); ?>"
                             onerror="this.src='assets/images/default-pet.svg'">
                        <button class="favorite-btn favorited" 
                                onclick="toggleFavorite(<?php echo $petItem['id']; ?>, this)">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <h5 class="pet-name"><?php echo htmlspecialchars($petItem['name']); ?></h5>
                        <span class="pet-species"><?php echo htmlspecialchars($petItem['species']); ?></span>
                        <p class="pet-breed"><?php echo htmlspecialchars($petItem['breed'] ?: 'Raça não informada'); ?></p>
                        <p class="pet-age">Idade: <?php echo $petItem['age'] ? htmlspecialchars($petItem['age']) . ' anos' : 'Não informada'; ?></p>
                        <p class="card-text"><?php echo htmlspecialchars(substr($petItem['description'] ?: 'Sem descrição', 0, 100)); ?><?php echo strlen($petItem['description']) > 100 ? '...' : ''; ?></p>
                        <small class="text-muted">Por: <?php echo htmlspecialchars($petItem['owner_username']); ?></small>
                        <br>
                        <small class="text-muted">Favoritado em: <?php echo date('d/m/Y', strtotime($petItem['favorited_at'])); ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        // Override toggle favorite to remove the card when unfavorited
        function toggleFavorite(petId, button) {
            if (isLoading) return;
            
            const isFavorited = button.classList.contains('favorited');
            const action = isFavorited ? 'remove' : 'add';
            
            button.disabled = true;
            
            fetch('api/favorites.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: action,
                    pet_id: petId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (action === 'remove') {
                        // Remove the card from favorites page
                        const card = button.closest('.col-md-6');
                        card.style.transition = 'opacity 0.3s ease-out';
                        card.style.opacity = '0';
                        setTimeout(() => {
                            card.remove();
                            // Update count
                            const badge = document.querySelector('.badge');
                            if (badge) {
                                const currentCount = parseInt(badge.textContent);
                                badge.textContent = (currentCount - 1) + ' favoritos';
                            }
                            // Check if no favorites left
                            const remaining = document.querySelectorAll('.pet-card').length;
                            if (remaining === 0) {
                                location.reload();
                            }
                        }, 300);
                    }
                    showMessage(data.message, 'success');
                } else {
                    showMessage(data.message, 'danger');
                }
            })
            .catch(error => {
                showMessage('Erro na conexão: ' + error.message, 'danger');
            })
            .finally(() => {
                button.disabled = false;
            });
        }
    </script>
</body>
</html>