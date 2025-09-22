<?php
session_start();

// Mock data for demonstration
$mock_pets = [
    [
        'id' => 1,
        'name' => 'Buddy',
        'species' => 'Dog',
        'breed' => 'Golden Retriever',
        'age' => 3,
        'description' => 'Um cão muito amigável e brincalhão, ideal para famílias com crianças. Adora brincar no quintal e fazer novos amigos.',
        'image_url' => 'assets/images/default-pet.svg',
        'owner_username' => 'Maria Silva',
        'created_at' => '2024-01-15 10:30:00',
        'is_favorited' => false
    ],
    [
        'id' => 2,
        'name' => 'Mia',
        'species' => 'Cat',
        'breed' => 'Siamese',
        'age' => 2,
        'description' => 'Gata carinhosa que adora estar perto dos donos. Muito limpa e independente.',
        'image_url' => 'assets/images/default-pet.svg',
        'owner_username' => 'João Santos',
        'created_at' => '2024-01-10 14:20:00',
        'is_favorited' => false
    ],
    [
        'id' => 3,
        'name' => 'Rex',
        'species' => 'Dog',
        'breed' => 'Pastor Alemão',
        'age' => 5,
        'description' => 'Cão protetor e leal, bem treinado. Perfeito para quem busca um companheiro fiel e guardião.',
        'image_url' => 'assets/images/default-pet.svg',
        'owner_username' => 'Ana Costa',
        'created_at' => '2024-01-08 09:15:00',
        'is_favorited' => false
    ],
    [
        'id' => 4,
        'name' => 'Luna',
        'species' => 'Cat',
        'breed' => 'Maine Coon',
        'age' => 1,
        'description' => 'Gatinha jovem e muito ativa. Adora brincar e explorar novos ambientes.',
        'image_url' => 'assets/images/default-pet.svg',
        'owner_username' => 'Pedro Oliveira',
        'created_at' => '2024-01-05 16:45:00',
        'is_favorited' => false
    ],
    [
        'id' => 5,
        'name' => 'Thor',
        'species' => 'Dog',
        'breed' => 'Rottweiler',
        'age' => 4,
        'description' => 'Cão forte e corajoso, muito leal à família. Precisa de um tutor experiente.',
        'image_url' => 'assets/images/default-pet.svg',
        'owner_username' => 'Carlos Mendes',
        'created_at' => '2024-01-02 11:30:00',
        'is_favorited' => false
    ],
    [
        'id' => 6,
        'name' => 'Bella',
        'species' => 'Cat',
        'breed' => 'Persa',
        'age' => 3,
        'description' => 'Gata elegante e tranquila, ideal para apartamentos. Adora carinho e mimos.',
        'image_url' => 'assets/images/default-pet.svg',
        'owner_username' => 'Lucia Ferreira',
        'created_at' => '2023-12-28 13:20:00',
        'is_favorited' => false
    ]
];

// Apply filters
$search = $_GET['search'] ?? '';
$species_filter = $_GET['species'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 12;

$filtered_pets = $mock_pets;

// Apply search filter
if (!empty($search)) {
    $filtered_pets = array_filter($filtered_pets, function($pet) use ($search) {
        return stripos($pet['name'], $search) !== false || 
               stripos($pet['breed'], $search) !== false || 
               stripos($pet['description'], $search) !== false;
    });
}

// Apply species filter
if (!empty($species_filter)) {
    $filtered_pets = array_filter($filtered_pets, function($pet) use ($species_filter) {
        return $pet['species'] === $species_filter;
    });
}

$total_pets = count($filtered_pets);
$total_pages = ceil($total_pets / $per_page);
$offset = ($page - 1) * $per_page;
$pets = array_slice($filtered_pets, $offset, $per_page);
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
    <!-- Demo Notice -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle"></i> <strong>Modo Demonstração:</strong> Este é o sistema BuscaPet funcionando com dados de exemplo. 
        Para usar completamente, configure um banco de dados MySQL.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="demo.php">
                <i class="fas fa-paw"></i> BuscaPet <span class="badge bg-warning text-dark">DEMO</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="demo.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showDemoMessage()">Meus Favoritos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showDemoMessage()">Meus Pets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showDemoMessage()">Adicionar Pet</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showDemoMessage()">Entrar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showDemoMessage()">Cadastrar</a>
                    </li>
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
                    
                    <form class="search-form" method="GET" action="demo.php">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Buscar por nome, raça..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" name="species">
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
        
        <div class="row">
            <?php if (empty($pets)): ?>
            <div class="col-12 text-center">
                <div class="py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Nenhum pet encontrado</h4>
                    <p class="text-muted">Tente ajustar os filtros de busca</p>
                </div>
            </div>
            <?php else: ?>
            <?php foreach ($pets as $pet): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card pet-card">
                    <div class="position-relative">
                        <img src="<?php echo $pet['image_url']; ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($pet['name']); ?>"
                             onerror="this.src='assets/images/default-pet.svg'">
                        <button class="favorite-btn" onclick="showDemoMessage()">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <h5 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></h5>
                        <span class="pet-species"><?php echo htmlspecialchars($pet['species']); ?></span>
                        <p class="pet-breed"><?php echo htmlspecialchars($pet['breed'] ?: 'Raça não informada'); ?></p>
                        <p class="pet-age">Idade: <?php echo $pet['age'] ? htmlspecialchars($pet['age']) . ' anos' : 'Não informada'; ?></p>
                        <p class="card-text"><?php echo htmlspecialchars(substr($pet['description'] ?: 'Sem descrição', 0, 100)); ?><?php echo strlen($pet['description']) > 100 ? '...' : ''; ?></p>
                        <small class="text-muted">Por: <?php echo htmlspecialchars($pet['owner_username']); ?></small>
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

    <!-- Features Section -->
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2>Funcionalidades do Sistema BuscaPet</h2>
                <p class="lead text-muted">Sistema completo de adoção de pets com recursos modernos</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-shield fa-3x text-primary"></i>
                    </div>
                    <h4>Sistema de Autenticação</h4>
                    <p class="text-muted">Login seguro, registro de usuários, proteção com hash e sessões</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-database fa-3x text-success"></i>
                    </div>
                    <h4>Banco de Dados Estruturado</h4>
                    <p class="text-muted">Tabelas relacionadas para usuários, pets, favoritos e logs</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-mobile-alt fa-3x text-warning"></i>
                    </div>
                    <h4>Design Responsivo</h4>
                    <p class="text-muted">Interface moderna com Bootstrap, funciona em todos os dispositivos</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-search fa-3x text-info"></i>
                    </div>
                    <h4>Busca Avançada</h4>
                    <p class="text-muted">Filtros por nome, raça, espécie com paginação inteligente</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-heart fa-3x text-danger"></i>
                    </div>
                    <h4>Sistema de Favoritos</h4>
                    <p class="text-muted">Salve seus pets favoritos e acesse facilmente</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-cloud-upload-alt fa-3x text-secondary"></i>
                    </div>
                    <h4>Upload de Imagens</h4>
                    <p class="text-muted">Sistema seguro de upload com validação e redimensionamento</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-paw"></i> BuscaPet <span class="badge bg-warning text-dark">DEMO</span></h5>
                    <p>Sistema completo de busca e adoção de pets desenvolvido com PHP, MySQL e Bootstrap.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 BuscaPet. Todos os direitos reservados.</p>
                    <p>
                        <a href="https://github.com/AllysonAraujo/BuscaPet" class="text-light me-3" target="_blank">
                            <i class="fab fa-github"></i> Código Fonte
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function showDemoMessage() {
            alert('Esta é uma demonstração! Para usar esta funcionalidade, configure um banco de dados MySQL e use as páginas completas do sistema.');
        }
        
        // Auto-dismiss demo notice
        setTimeout(function() {
            const alert = document.querySelector('.alert-info');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 8000);
    </script>
</body>
</html>