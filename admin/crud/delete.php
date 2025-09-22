<?php
require_once '../../includes/auth.php';
requireLogin();

$pageTitle = "Excluir Pet";
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

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete']) && $pet) {
    try {
        $stmt = $conn->prepare("DELETE FROM pets WHERE id = ?");
        
        if ($stmt->execute([$petId])) {
            $_SESSION['success_message'] = 'Pet "' . $pet['name'] . '" foi excluído com sucesso.';
            header("Location: /admin/crud/read.php");
            exit();
        } else {
            $message = 'Erro ao excluir pet.';
            $messageType = 'danger';
        }
    } catch (Exception $e) {
        $message = 'Erro na conexão: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

require_once '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-trash text-danger"></i> Excluir Pet</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/crud/read.php">Pets</a></li>
            <li class="breadcrumb-item active">Excluir</li>
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
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Confirmar Exclusão
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-danger" role="alert">
                    <strong><i class="fas fa-warning"></i> Atenção!</strong><br>
                    Esta ação não pode ser desfeita. O pet será permanentemente removido do sistema.
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <h4>Você tem certeza que deseja excluir este pet?</h4>
                        
                        <div class="card bg-light mt-3">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-paw"></i> 
                                    <strong><?php echo htmlspecialchars($pet['name']); ?></strong>
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="card-text">
                                            <strong>Espécie:</strong> 
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
                                        </p>
                                        
                                        <?php if ($pet['breed']): ?>
                                            <p class="card-text">
                                                <strong>Raça:</strong> <?php echo htmlspecialchars($pet['breed']); ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if ($pet['age']): ?>
                                            <p class="card-text">
                                                <strong>Idade:</strong> 
                                                <?php 
                                                $years = floor($pet['age'] / 12);
                                                $months = $pet['age'] % 12;
                                                if ($years > 0) {
                                                    echo $years . ' ano(s)';
                                                    if ($months > 0) echo ' e ' . $months . ' mês(es)';
                                                } else {
                                                    echo $months . ' mês(es)';
                                                }
                                                ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <p class="card-text">
                                            <strong>Status:</strong> 
                                            <?php
                                            $statusLabels = [
                                                'available' => 'Disponível',
                                                'pending' => 'Pendente',
                                                'adopted' => 'Adotado'
                                            ];
                                            echo $statusLabels[$pet['status']] ?? ucfirst($pet['status']);
                                            ?>
                                        </p>
                                        
                                        <p class="card-text">
                                            <strong>Cadastrado em:</strong><br>
                                            <?php echo date('d/m/Y H:i', strtotime($pet['created_at'])); ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <?php if ($pet['description']): ?>
                                    <div class="mt-3">
                                        <strong>Descrição:</strong>
                                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($pet['description'])); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <form method="POST" action="" class="mt-4">
                            <div class="d-flex gap-3">
                                <button type="submit" name="confirm_delete" value="1" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Sim, Excluir Pet
                                </button>
                                
                                <a href="/admin/crud/read.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                                
                                <a href="/admin/crud/update.php?id=<?php echo $pet['id']; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i> Editar ao Invés de Excluir
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-header bg-warning">
                                <h6 class="mb-0">
                                    <i class="fas fa-lightbulb"></i> Alternativas
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="small text-muted">
                                    Antes de excluir, considere:
                                </p>
                                <ul class="small">
                                    <li>Alterar o status para "Adotado" se o pet foi adotado</li>
                                    <li>Editar as informações se estiverem incorretas</li>
                                    <li>Manter o registro para histórico</li>
                                </ul>
                                
                                <div class="d-grid mt-3">
                                    <a href="/admin/crud/update.php?id=<?php echo $pet['id']; ?>" class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-edit"></i> Editar Pet
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
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