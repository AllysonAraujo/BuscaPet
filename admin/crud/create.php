<?php
require_once '../../includes/auth.php';
requireLogin();

$pageTitle = "Cadastrar Pet";
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            require_once '../../config/database.php';
            $database = new Database();
            $conn = $database->getConnection();
            
            $currentUser = getCurrentUser();
            
            $stmt = $conn->prepare("INSERT INTO pets (name, species, breed, age, description, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$name, $species, $breed, $age, $description, $status, $currentUser['id']])) {
                $message = 'Pet cadastrado com sucesso!';
                $messageType = 'success';
                
                // Clear form data
                $_POST = [];
            } else {
                $message = 'Erro ao cadastrar pet.';
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
    <h1><i class="fas fa-plus"></i> Cadastrar Pet</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Cadastrar Pet</li>
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

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-paw"></i> Informações do Pet</h5>
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
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
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
                                <option value="dog" <?php echo ($_POST['species'] ?? '') === 'dog' ? 'selected' : ''; ?>>Cão</option>
                                <option value="cat" <?php echo ($_POST['species'] ?? '') === 'cat' ? 'selected' : ''; ?>>Gato</option>
                                <option value="rabbit" <?php echo ($_POST['species'] ?? '') === 'rabbit' ? 'selected' : ''; ?>>Coelho</option>
                                <option value="bird" <?php echo ($_POST['species'] ?? '') === 'bird' ? 'selected' : ''; ?>>Ave</option>
                                <option value="other" <?php echo ($_POST['species'] ?? '') === 'other' ? 'selected' : ''; ?>>Outros</option>
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
                                   value="<?php echo htmlspecialchars($_POST['breed'] ?? ''); ?>"
                                   placeholder="Digite a raça (opcional)">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="age" class="form-label">Idade (em meses)</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="age" 
                                   name="age" 
                                   value="<?php echo $_POST['age'] ?? ''; ?>"
                                   min="0" 
                                   max="300"
                                   placeholder="Idade em meses">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="available" <?php echo ($_POST['status'] ?? 'available') === 'available' ? 'selected' : ''; ?>>Disponível</option>
                            <option value="pending" <?php echo ($_POST['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pendente</option>
                            <option value="adopted" <?php echo ($_POST['status'] ?? '') === 'adopted' ? 'selected' : ''; ?>>Adotado</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  placeholder="Descreva o pet, suas características, personalidade, etc."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-loading">
                            <i class="fas fa-save"></i> Cadastrar Pet
                        </button>
                        <a href="/admin/dashboard.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Dicas</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        Use nomes simples e memoráveis
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        Seja específico na descrição
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        Informe características especiais
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        Mantenha o status atualizado
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Ações Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/admin/crud/read.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-list"></i> Ver Todos os Pets
                    </a>
                    <a href="/api/petfinder.php" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-search"></i> Buscar na API
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>