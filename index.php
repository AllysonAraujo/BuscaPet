<?php
session_start();
require_once 'src/Pet.php';
require_once 'src/Favorite.php';

$pet = new Pet();
$favorite = new Favorite();

// Get search parameters
$search = $_GET['search'] ?? '';
$species_filter = $_GET['species'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Get pets with search and pagination
$pets = $pet->getAll($per_page, $offset, $search, $species_filter);
$total_pets = $pet->getCount($search, $species_filter);
$total_pages = ceil($total_pets / $per_page);

// Check favorites for logged in users
if (isset($_SESSION['user_id'])) {
    foreach ($pets as &$petItem) {
        $petItem['is_favorited'] = $favorite->isFavorited($_SESSION['user_id'], $petItem['id']);
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuscaPet - Encontre seu melhor amigo</title>
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
                        <a class="nav-link active" href="index.php">Início</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="favorites.php">Meus Favoritos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my-pets.php">Meus Pets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add-pet.php">Adicionar Pet</a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
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
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Entrar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Cadastrar</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Search Section -->
    <div class="search-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-4">
                        <h1 class="display-4 fw-bold mb-3">Encontre seu melhor amigo</h1>
                        <p class="lead">Conectando pets com famílias amorosas</p>
                    </div>
                    
                    <form class="search-form" id="searchForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Buscar por nome, raça..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="species" name="species">
                                    <option value="">Todas as espécies</option>
                                    <option value="Dog" <?php echo $species_filter === 'Dog' ? 'selected' : ''; ?>>Cães</option>
                                    <option value="Cat" <?php echo $species_filter === 'Cat' ? 'selected' : ''; ?>>Gatos</option>
                                    <option value="Bird" <?php echo $species_filter === 'Bird' ? 'selected' : ''; ?>>Pássaros</option>
                                    <option value="Other" <?php echo $species_filter === 'Other' ? 'selected' : ''; ?>>Outros</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer" class="container mt-3"></div>

    <!-- Loading Indicator -->
    <div id="loading" class="loading">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="mt-2">Carregando pets...</p>
    </div>

    <!-- Pets Grid -->
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Pets Disponíveis</h2>
                    <span class="badge bg-primary fs-6"><?php echo $total_pets; ?> pets encontrados</span>
                </div>
            </div>
        </div>
        
        <div class="row" id="petsContainer">
            <?php if (empty($pets)): ?>
            <div class="col-12 text-center">
                <div class="py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Nenhum pet encontrado</h4>
                    <p class="text-muted">Tente ajustar os filtros de busca</p>
                </div>
            </div>
            <?php else: ?>
            <?php foreach ($pets as $petItem): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card pet-card">
                    <div class="position-relative">
                        <img src="<?php echo $petItem['image_url'] ?: 'assets/images/default-pet.jpg'; ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($petItem['name']); ?>"
                             onerror="this.src='assets/images/default-pet.jpg'">
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <button class="favorite-btn <?php echo $petItem['is_favorited'] ? 'favorited' : ''; ?>" 
                                onclick="toggleFavorite(<?php echo $petItem['id']; ?>, this)">
                            <i class="fas fa-heart"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="pet-name"><?php echo htmlspecialchars($petItem['name']); ?></h5>
                        <span class="pet-species"><?php echo htmlspecialchars($petItem['species']); ?></span>
                        <p class="pet-breed"><?php echo htmlspecialchars($petItem['breed'] ?: 'Raça não informada'); ?></p>
                        <p class="pet-age">Idade: <?php echo $petItem['age'] ? htmlspecialchars($petItem['age']) . ' anos' : 'Não informada'; ?></p>
                        <p class="card-text"><?php echo htmlspecialchars(substr($petItem['description'] ?: 'Sem descrição', 0, 100)); ?><?php echo strlen($petItem['description']) > 100 ? '...' : ''; ?></p>
                        <small class="text-muted">Por: <?php echo htmlspecialchars($petItem['owner_username']); ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="row mt-4">
            <div class="col-12">
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Anterior</a>
                        </li>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        for ($i = $start_page; $i <= $end_page; $i++):
                        ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Próximo</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-paw"></i> BuscaPet</h5>
                    <p>Conectando pets com famílias amorosas desde 2024.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 BuscaPet. Todos os direitos reservados.</p>
                    <p>
                        <a href="#" class="text-light me-3">Política de Privacidade</a>
                        <a href="#" class="text-light">Termos de Uso</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>