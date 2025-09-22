<?php
require_once '../../includes/auth.php';
requireLogin();

$pageTitle = "Listar Pets";

// Pagination
$page = (int)($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// Search and filter
$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$speciesFilter = $_GET['species'] ?? '';

try {
    require_once '../../config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    
    if (!empty($search)) {
        $whereConditions[] = "(name LIKE ? OR breed LIKE ? OR description LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
    }
    
    if (!empty($statusFilter)) {
        $whereConditions[] = "status = ?";
        $params[] = $statusFilter;
    }
    
    if (!empty($speciesFilter)) {
        $whereConditions[] = "species = ?";
        $params[] = $speciesFilter;
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    // Get total count
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pets $whereClause");
    $stmt->execute($params);
    $totalRecords = $stmt->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);
    
    // Get pets
    $stmt = $conn->prepare("
        SELECT p.*, u.username as created_by_name 
        FROM pets p 
        LEFT JOIN users u ON p.created_by = u.id 
        $whereClause 
        ORDER BY p.created_at DESC 
        LIMIT $limit OFFSET $offset
    ");
    $stmt->execute($params);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $pets = [];
    $totalRecords = 0;
    $totalPages = 0;
    $error = $e->getMessage();
}

require_once '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-list"></i> Pets Cadastrados</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Listar Pets</li>
        </ol>
    </nav>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="<?php echo htmlspecialchars($search); ?>"
                       placeholder="Nome, raça ou descrição">
            </div>
            
            <div class="col-md-3">
                <label for="species" class="form-label">Espécie</label>
                <select class="form-control" id="species" name="species">
                    <option value="">Todas as espécies</option>
                    <option value="dog" <?php echo $speciesFilter === 'dog' ? 'selected' : ''; ?>>Cão</option>
                    <option value="cat" <?php echo $speciesFilter === 'cat' ? 'selected' : ''; ?>>Gato</option>
                    <option value="rabbit" <?php echo $speciesFilter === 'rabbit' ? 'selected' : ''; ?>>Coelho</option>
                    <option value="bird" <?php echo $speciesFilter === 'bird' ? 'selected' : ''; ?>>Ave</option>
                    <option value="other" <?php echo $speciesFilter === 'other' ? 'selected' : ''; ?>>Outros</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="">Todos os status</option>
                    <option value="available" <?php echo $statusFilter === 'available' ? 'selected' : ''; ?>>Disponível</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pendente</option>
                    <option value="adopted" <?php echo $statusFilter === 'adopted' ? 'selected' : ''; ?>>Adotado</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="/admin/crud/read.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Results -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-paw"></i> 
            <?php echo $totalRecords; ?> pet(s) encontrado(s)
        </h5>
        <a href="/admin/crud/create.php" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Novo Pet
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($pets)): ?>
            <div class="text-center py-5">
                <i class="fas fa-paw text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">Nenhum pet encontrado</h4>
                <?php if (empty($search) && empty($statusFilter) && empty($speciesFilter)): ?>
                    <p class="text-muted">Comece cadastrando seu primeiro pet.</p>
                    <a href="/admin/crud/create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Cadastrar Pet
                    </a>
                <?php else: ?>
                    <p class="text-muted">Tente ajustar os filtros de busca.</p>
                    <a href="/admin/crud/read.php" class="btn btn-outline-primary">
                        <i class="fas fa-times"></i> Limpar Filtros
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Espécie</th>
                            <th>Raça</th>
                            <th>Idade</th>
                            <th>Status</th>
                            <th>Cadastrado em</th>
                            <th>Por</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pets as $pet): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($pet['name']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?php 
                                        $speciesLabels = [
                                            'dog' => 'Cão',
                                            'cat' => 'Gato',
                                            'rabbit' => 'Coelho',
                                            'bird' => 'Ave',
                                            'other' => 'Outros'
                                        ];
                                        echo $speciesLabels[$pet['species']] ?? ucfirst($pet['species']);
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($pet['breed'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php 
                                    if ($pet['age']) {
                                        $years = floor($pet['age'] / 12);
                                        $months = $pet['age'] % 12;
                                        if ($years > 0) {
                                            echo $years . 'a';
                                            if ($months > 0) echo ' ' . $months . 'm';
                                        } else {
                                            echo $months . 'm';
                                        }
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'available' => 'success',
                                        'pending' => 'warning',
                                        'adopted' => 'info'
                                    ];
                                    $statusLabels = [
                                        'available' => 'Disponível',
                                        'pending' => 'Pendente',
                                        'adopted' => 'Adotado'
                                    ];
                                    $color = $statusColors[$pet['status']] ?? 'secondary';
                                    $label = $statusLabels[$pet['status']] ?? ucfirst($pet['status']);
                                    ?>
                                    <span class="badge bg-<?php echo $color; ?>">
                                        <?php echo $label; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($pet['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($pet['created_by_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/admin/crud/update.php?id=<?php echo $pet['id']; ?>" 
                                           class="btn btn-outline-primary btn-sm" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/admin/crud/delete.php?id=<?php echo $pet['id']; ?>" 
                                           class="btn btn-outline-danger btn-sm btn-delete" 
                                           title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Navegação de páginas" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>&species=<?php echo urlencode($speciesFilter); ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>&species=<?php echo urlencode($speciesFilter); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>&species=<?php echo urlencode($speciesFilter); ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>