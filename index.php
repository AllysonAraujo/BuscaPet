<?php
$pageTitle = "Início";
require_once 'includes/header.php';
?>

<div class="hero-section bg-primary-light py-5 mb-5 rounded-custom">
    <div class="text-center">
        <div class="hero-icon mb-4">
            <i class="fas fa-paw text-primary-custom" style="font-size: 4rem;"></i>
        </div>
        <h1 class="display-4 text-primary-custom fw-bold mb-3">BuscaPet</h1>
        <p class="lead mb-4">Encontre seu companheiro perfeito através da nossa plataforma de busca de pets para adoção</p>
        <div class="hero-actions">
            <a href="/api/petfinder.php" class="btn btn-primary btn-lg me-3">
                <i class="fas fa-search"></i> Buscar Pets
            </a>
            <?php if (!getCurrentUser()): ?>
                <a href="/login.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Fazer Login
                </a>
            <?php else: ?>
                <a href="/admin/dashboard.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <div class="mb-3">
                    <i class="fas fa-heart text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Adoção Responsável</h5>
                <p class="card-text">Promovemos a adoção responsável conectando pets que precisam de um lar com famílias amorosas.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <div class="mb-3">
                    <i class="fas fa-search text-info" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Busca Avançada</h5>
                <p class="card-text">Utilize nossa busca avançada para encontrar o pet ideal baseado em espécie, raça, idade e localização.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <div class="mb-3">
                    <i class="fas fa-users text-success" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Comunidade</h5>
                <p class="card-text">Faça parte de uma comunidade dedicada ao bem-estar animal e à promoção da adoção.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Como Funciona</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-paw text-primary"></i> Para Adotantes</h6>
                        <ol>
                            <li>Navegue pelos pets disponíveis</li>
                            <li>Use os filtros para refinar sua busca</li>
                            <li>Entre em contato com a organização</li>
                            <li>Conheça seu novo companheiro</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-building text-primary"></i> Para Organizações</h6>
                        <ol>
                            <li>Cadastre-se na plataforma</li>
                            <li>Adicione os pets disponíveis</li>
                            <li>Gerencie solicitações de adoção</li>
                            <li>Acompanhe o processo de adoção</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Estatísticas</h5>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="mb-3">
                        <h3 class="fw-bold">1000+</h3>
                        <small>Pets Cadastrados</small>
                    </div>
                    <div class="mb-3">
                        <h3 class="fw-bold">500+</h3>
                        <small>Adoções Realizadas</small>
                    </div>
                    <div class="mb-3">
                        <h3 class="fw-bold">50+</h3>
                        <small>Organizações Parceiras</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-phone"></i> Precisa de Ajuda?</h6>
            </div>
            <div class="card-body">
                <p class="card-text small">Entre em contato conosco para dúvidas sobre adoção ou para cadastrar sua organização.</p>
                <a href="mailto:contato@buscapet.com" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-envelope"></i> Contato
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>