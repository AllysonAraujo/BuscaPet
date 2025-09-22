<?php
$pageTitle = "Buscar Pets - API Petfinder";

// Handle search parameters
$search = trim($_GET['search'] ?? '');
$type = $_GET['type'] ?? '';
$location = trim($_GET['location'] ?? '');
$page = (int)($_GET['page'] ?? 1);

$pets = [];
$totalCount = 0;
$error = '';

// If search is requested
if (!empty($search) || !empty($type) || !empty($location)) {
    try {
        require_once '../config/petfinder_api.php';
        
        $petfinderAPI = new PetfinderAPI();
        
        // Build search parameters
        $searchParams = [];
        
        if (!empty($search)) {
            $searchParams['name'] = $search;
        }
        
        if (!empty($type)) {
            $searchParams['type'] = $type;
        }
        
        if (!empty($location)) {
            $searchParams['location'] = $location;
        }
        
        $searchParams['page'] = $page;
        $searchParams['limit'] = 20;
        
        // Search for animals
        $result = $petfinderAPI->searchAnimals($searchParams);
        
        if ($result && isset($result['animals'])) {
            $pets = $result['animals'];
            $totalCount = $result['pagination']['total_count'] ?? 0;
        } else {
            $error = 'Nenhum resultado encontrado ou erro na API.';
        }
        
    } catch (Exception $e) {
        $error = 'Erro ao conectar com a API Petfinder: ' . $e->getMessage();
        
        // Show demo data if API is not configured
        if (strpos($e->getMessage(), 'your_api_key_here') !== false) {
            $error = 'API Petfinder não configurada. Mostrando dados de demonstração.';
            
            // Demo pets data
            $pets = [
                [
                    'id' => 1,
                    'name' => 'Buddy',
                    'species' => 'Dog',
                    'breeds' => ['primary' => 'Golden Retriever', 'secondary' => null],
                    'age' => 'Adult',
                    'gender' => 'Male',
                    'size' => 'Large',
                    'description' => 'Buddy é um cão amigável e carinhoso que adora brincar.',
                    'photos' => [
                        ['medium' => 'https://via.placeholder.com/300x200?text=Buddy']
                    ],
                    'contact' => [
                        'phone' => '(11) 99999-9999',
                        'email' => 'contato@abrigo.com'
                    ],
                    'status' => 'adoptable'
                ],
                [
                    'id' => 2,
                    'name' => 'Luna',
                    'species' => 'Cat',
                    'breeds' => ['primary' => 'Siamese', 'secondary' => null],
                    'age' => 'Young',
                    'gender' => 'Female',
                    'size' => 'Medium',
                    'description' => 'Luna é uma gatinha carinhosa e independente.',
                    'photos' => [
                        ['medium' => 'https://via.placeholder.com/300x200?text=Luna']
                    ],
                    'contact' => [
                        'phone' => '(11) 88888-8888',
                        'email' => 'adocao@abrigo.com'
                    ],
                    'status' => 'adoptable'
                ],
                [
                    'id' => 3,
                    'name' => 'Rex',
                    'species' => 'Dog',
                    'breeds' => ['primary' => 'German Shepherd', 'secondary' => null],
                    'age' => 'Adult',
                    'gender' => 'Male',
                    'size' => 'Large',
                    'description' => 'Rex é um cão protetor e leal, perfeito para famílias.',
                    'photos' => [
                        ['medium' => 'https://via.placeholder.com/300x200?text=Rex']
                    ],
                    'contact' => [
                        'phone' => '(11) 77777-7777',
                        'email' => 'pets@abrigo.com'
                    ],
                    'status' => 'adoptable'
                ]
            ];
            
            $totalCount = 3;
        }
    }
}

require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-search"></i> Buscar Pets</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item active">Buscar Pets</li>
        </ol>
    </nav>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros de Busca</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Nome do Pet</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="<?php echo htmlspecialchars($search); ?>"
                       placeholder="Digite o nome do pet">
            </div>
            
            <div class="col-md-3">
                <label for="type" class="form-label">Tipo</label>
                <select class="form-control" id="type" name="type">
                    <option value="">Todos os tipos</option>
                    <option value="dog" <?php echo $type === 'dog' ? 'selected' : ''; ?>>Cão</option>
                    <option value="cat" <?php echo $type === 'cat' ? 'selected' : ''; ?>>Gato</option>
                    <option value="rabbit" <?php echo $type === 'rabbit' ? 'selected' : ''; ?>>Coelho</option>
                    <option value="bird" <?php echo $type === 'bird' ? 'selected' : ''; ?>>Ave</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="location" class="form-label">Localização</label>
                <input type="text" 
                       class="form-control" 
                       id="location" 
                       name="location" 
                       value="<?php echo htmlspecialchars($location); ?>"
                       placeholder="CEP ou cidade">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="/api/petfinder.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Error Message -->
<?php if ($error): ?>
    <div class="alert alert-info" role="alert">
        <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Results -->
<?php if (!empty($pets)): ?>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-paw"></i> 
                <?php echo $totalCount; ?> pet(s) encontrado(s)
                <?php if (!empty($search) || !empty($type) || !empty($location)): ?>
                    <small class="text-muted">
                        <?php 
                        $filters = [];
                        if ($search) $filters[] = "nome: \"$search\"";
                        if ($type) $filters[] = "tipo: \"$type\"";
                        if ($location) $filters[] = "local: \"$location\"";
                        echo "(" . implode(", ", $filters) . ")";
                        ?>
                    </small>
                <?php endif; ?>
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($pets as $pet): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card pet-card h-100">
                            <?php if (!empty($pet['photos'])): ?>
                                <img src="<?php echo htmlspecialchars($pet['photos'][0]['medium'] ?? $pet['photos'][0]['large'] ?? 'https://via.placeholder.com/300x200?text=Pet'); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($pet['name']); ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-paw fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($pet['name']); ?>
                                    <span class="badge bg-primary ms-2">
                                        <?php echo htmlspecialchars($pet['species'] ?? 'Pet'); ?>
                                    </span>
                                </h5>
                                
                                <div class="mb-2">
                                    <?php if (!empty($pet['breeds']['primary'])): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-dna"></i> 
                                            <?php echo htmlspecialchars($pet['breeds']['primary']); ?>
                                        </small><br>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($pet['age'])): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-birthday-cake"></i> 
                                            <?php echo htmlspecialchars($pet['age']); ?>
                                        </small>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($pet['gender'])): ?>
                                        <small class="text-muted ms-2">
                                            <i class="fas fa-venus-mars"></i> 
                                            <?php echo htmlspecialchars($pet['gender']); ?>
                                        </small>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($pet['size'])): ?>
                                        <small class="text-muted ms-2">
                                            <i class="fas fa-ruler"></i> 
                                            <?php echo htmlspecialchars($pet['size']); ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($pet['description'])): ?>
                                    <p class="card-text">
                                        <?php 
                                        $description = htmlspecialchars($pet['description']);
                                        echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                                        ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (!empty($pet['contact'])): ?>
                                    <div class="mt-3">
                                        <h6>Contato:</h6>
                                        <?php if (!empty($pet['contact']['phone'])): ?>
                                            <small>
                                                <i class="fas fa-phone"></i> 
                                                <?php echo htmlspecialchars($pet['contact']['phone']); ?>
                                            </small><br>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($pet['contact']['email'])): ?>
                                            <small>
                                                <i class="fas fa-envelope"></i> 
                                                <a href="mailto:<?php echo htmlspecialchars($pet['contact']['email']); ?>">
                                                    <?php echo htmlspecialchars($pet['contact']['email']); ?>
                                                </a>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-footer">
                                <small class="text-muted">
                                    <i class="fas fa-heart"></i> 
                                    Status: <?php echo ucfirst($pet['status'] ?? 'Disponível'); ?>
                                </small>
                                
                                <?php if (!empty($pet['contact']['email'])): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($pet['contact']['email']); ?>?subject=Interesse em adotar <?php echo htmlspecialchars($pet['name']); ?>" 
                                       class="btn btn-primary btn-sm float-end">
                                        <i class="fas fa-heart"></i> Interessado
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php elseif (!empty($search) || !empty($type) || !empty($location)): ?>
    <div class="text-center py-5">
        <i class="fas fa-search text-muted" style="font-size: 4rem;"></i>
        <h4 class="text-muted mt-3">Nenhum pet encontrado</h4>
        <p class="text-muted">Tente ajustar os filtros de busca ou verificar a conexão com a API.</p>
        <a href="/api/petfinder.php" class="btn btn-primary">
            <i class="fas fa-search"></i> Nova Busca
        </a>
    </div>

<?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-paw text-muted" style="font-size: 4rem;"></i>
        <h4 class="text-muted mt-3">Busque por pets para adoção</h4>
        <p class="text-muted">Use os filtros acima para encontrar seu companheiro perfeito.</p>
        
        <div class="row justify-content-center mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6><i class="fas fa-info-circle"></i> Como usar a busca:</h6>
                        <ul class="text-start">
                            <li>Digite o nome do pet que você procura</li>
                            <li>Selecione o tipo de animal</li>
                            <li>Informe sua localização para buscar pets próximos</li>
                            <li>Clique em "Buscar" para ver os resultados</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>