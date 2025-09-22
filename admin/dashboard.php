<?php
require_once '../includes/auth.php';
requireLogin();

$pageTitle = "Dashboard";

// Get some basic stats
try {
    require_once '../config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    // Count total pets
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pets");
    $stmt->execute();
    $totalPets = $stmt->fetchColumn();
    
    // Count total users
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
    $stmt->execute();
    $totalUsers = $stmt->fetchColumn();
    
    // Get recent pets
    $stmt = $conn->prepare("SELECT * FROM pets ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $recentPets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $totalPets = 0;
    $totalUsers = 0;
    $recentPets = [];
}

require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
    <div>
        <span class="text-muted">Bem-vindo, <strong><?php echo htmlspecialchars(getCurrentUser()['username']); ?></strong></span>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row dashboard-stats mb-4">
    <div class="col-md-3 mb-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Total de Pets</h6>
                        <h3 class="text-primary"><?php echo $totalPets; ?></h3>
                    </div>
                    <div class="stats-icon text-primary">
                        <i class="fas fa-paw"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Usuários</h6>
                        <h3 class="text-info"><?php echo $totalUsers; ?></h3>
                    </div>
                    <div class="stats-icon text-info">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Disponíveis</h6>
                        <h3 class="text-success"><?php echo $totalPets; ?></h3>
                    </div>
                    <div class="stats-icon text-success">
                        <i class="fas fa-heart"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">Adotados</h6>
                        <h3 class="text-warning">0</h3>
                    </div>
                    <div class="stats-icon text-warning">
                        <i class="fas fa-home"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="/admin/crud/create.php" class="btn btn-primary w-100">
                            <i class="fas fa-plus"></i> Novo Pet
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/admin/crud/read.php" class="btn btn-info w-100">
                            <i class="fas fa-list"></i> Listar Pets
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/api/petfinder.php" class="btn btn-success w-100">
                            <i class="fas fa-search"></i> Busca Externa
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/includes/auth.php?action=logout" class="btn btn-outline-danger w-100">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Pets -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Pets Recentes</h5>
                <a href="/admin/crud/read.php" class="btn btn-sm btn-outline-primary">Ver Todos</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentPets)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-paw text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">Nenhum pet cadastrado ainda.</p>
                        <a href="/admin/crud/create.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Cadastrar Primeiro Pet
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Espécie</th>
                                    <th>Raça</th>
                                    <th>Status</th>
                                    <th>Cadastrado</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentPets as $pet): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($pet['name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($pet['species']); ?></td>
                                        <td><?php echo htmlspecialchars($pet['breed'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-success">
                                                <?php echo ucfirst($pet['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($pet['created_at'])); ?></td>
                                        <td>
                                            <a href="/admin/crud/update.php?id=<?php echo $pet['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Sistema</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-database text-success"></i> 
                        <strong>Banco:</strong> Conectado
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-cloud text-info"></i> 
                        <strong>API:</strong> Configurada
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-shield-alt text-warning"></i> 
                        <strong>Auth:</strong> Ativo
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-server text-primary"></i> 
                        <strong>Status:</strong> Online
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-question-circle"></i> Ajuda</h6>
            </div>
            <div class="card-body">
                <p class="card-text small">
                    Use o sistema de CRUD para gerenciar pets e a API Petfinder para buscar pets externos.
                </p>
                <ul class="small">
                    <li>Cadastrar novos pets</li>
                    <li>Editar informações</li>
                    <li>Buscar na API externa</li>
                    <li>Gerenciar status</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>