    </main>

    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-paw"></i> BuscaPet</h5>
                    <p>Sistema de busca e gerenciamento de pets para adoção.</p>
                </div>
                <div class="col-md-6">
                    <h6>Links Úteis</h6>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-light text-decoration-none">Início</a></li>
                        <li><a href="/api/petfinder.php" class="text-light text-decoration-none">Buscar Pets</a></li>
                        <?php if (getCurrentUser()): ?>
                            <li><a href="/admin/dashboard.php" class="text-light text-decoration-none">Dashboard</a></li>
                        <?php else: ?>
                            <li><a href="/login.php" class="text-light text-decoration-none">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col text-center">
                    <p>&copy; <?php echo date('Y'); ?> BuscaPet. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="/assets/js/main.js"></script>
    
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>