<?php
require_once '../../includes/auth.php';
requireLogin();

$pageTitle = "Editar Pet";
$message = '';
$messageType = '';
$pet = null;

// Get pet ID
$petId = (int)($_GET['id'] ?? 0);

if (!$petId) {
    header("Location: /admin/crud/read.php");
    exit();
}

try {
    require_once '../../config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    // Get pet data
    $stmt = $conn->prepare("SELECT * FROM pets WHERE id = ?");
    $stmt->execute([$petId]);
    $pet = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pet) {
        header("Location: /admin/crud/read.php");
        exit();
    }
    
} catch (Exception $e) {
    $message = 'Erro na conexão: ' . $e->getMessage();
    $messageType = 'danger';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $pet) {
    $name = trim($_POST['name'] ?? '');
    $species = trim($_POST['species'] ?? '');
    $breed = trim($_POST['breed'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'available';
    
    if (empty($name) || empty($species)) {
        $message = 'Nome e espécie são obrigatórios.';
        $messageType = 'danger';
    } else {
        try {
            $stmt = $conn->prepare("UPDATE pets SET name = ?, species = ?, breed = ?, age = ?, description = ?, status = ?, updated_at = NOW() WHERE id = ?");
            
            if ($stmt->execute([$name, $species, $breed, $age, $description, $status, $petId])) {
                $message = 'Pet atualizado com sucesso!';
                $messageType = 'success';
                
                // Update pet data for display
                $pet['name'] = $name;
                $pet['species'] = $species;
                $pet['breed'] = $breed;
                $pet['age'] = $age;
                $pet['description'] = $description;
                $pet['status'] = $status;
                
            } else {
                $message = 'Erro ao atualizar pet.';
                $messageType = 'danger';
            }
        } catch (Exception $e) {
            $message = 'Erro na conexão: ' . $e->getMessage();
            $messageType = 'danger';
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-edit"></i> Editar Pet</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/crud/read.php">Pets</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </nav>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($pet): ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-paw"></i> Informações do Pet: <?php echo htmlspecialchars($pet['name']); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nome *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo htmlspecialchars($pet['name']); ?>"
                                   placeholder="Digite o nome do pet"
                                   required>
                            <div class="invalid-feedback">
                                Por favor, informe o nome do pet.
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="species" class="form-label">Espécie *</label>
                            <select class="form-control" id="species" name="species" required>
                                <option value="">Selecione a espécie</option>
                                <option value="dog" <?php echo $pet['species'] === 'dog' ? 'selected' : ''; ?>>Cão</option>
                                <option value="cat" <?php echo $pet['species'] === 'cat' ? 'selected' : ''; ?>>Gato</option>
                                <option value="rabbit" <?php echo $pet['species'] === 'rabbit' ? 'selected' : ''; ?>>Coelho</option>
                                <option value="bird" <?php echo $pet['species'] === 'bird' ? 'selected' : ''; ?>>Ave</option>
                                <option value="other" <?php echo $pet['species'] === 'other' ? 'selected' : ''; ?>>Outros</option>
                            </select>
                            <div class="invalid-feedback">
                                Por favor, selecione a espécie do pet.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="breed" class="form-label">Raça</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="breed" 
                                   name="breed" 
                                   value="<?php echo htmlspecialchars($pet['breed'] ?? ''); ?>"
                                   placeholder="Digite a raça (opcional)">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="age" class="form-label">Idade (em meses)</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="age" 
                                   name="age" 
                                   value="<?php echo $pet['age']; ?>"
                                   min="0" 
                                   max="300"
                                   placeholder="Idade em meses">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="available" <?php echo $pet['status'] === 'available' ? 'selected' : ''; ?>>Disponível</option>
                            <option value="pending" <?php echo $pet['status'] === 'pending' ? 'selected' : ''; ?>>Pendente</option>
                            <option value="adopted" <?php echo $pet['status'] === 'adopted' ? 'selected' : ''; ?>>Adotado</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  placeholder="Descreva o pet, suas características, personalidade, etc."><?php echo htmlspecialchars($pet['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-loading">
                            <i class="fas fa-save"></i> Atualizar Pet
                        </button>
                        <a href="/admin/crud/read.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <a href="/admin/crud/delete.php?id=<?php echo $pet['id']; ?>" 
                           class="btn btn-outline-danger btn-delete ms-auto">
                            <i class="fas fa-trash"></i> Excluir
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informações</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><strong>ID:</strong> <?php echo $pet['id']; ?></li>
                    <li><strong>Cadastrado em:</strong><br>
                        <?php echo date('d/m/Y H:i', strtotime($pet['created_at'])); ?>
                    </li>
                    <?php if ($pet['updated_at'] && $pet['updated_at'] !== $pet['created_at']): ?>
                        <li><strong>Atualizado em:</strong><br>
                            <?php echo date('d/m/Y H:i', strtotime($pet['updated_at'])); ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Ações Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/admin/crud/create.php" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-plus"></i> Novo Pet
                    </a>
                    <a href="/admin/crud/read.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-list"></i> Ver Todos
                    </a>
                    <a href="/api/petfinder.php" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-search"></i> Buscar na API
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i> Pet não encontrado.
</div>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>