<?php
require_once 'includes/auth.php';

// Redirect if already logged in
redirectIfLoggedIn();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        $auth = new Auth();
        if ($auth->login($username, $password)) {
            header("Location: /admin/dashboard.php");
            exit();
        } else {
            $error = 'Usuário ou senha inválidos.';
        }
    }
}

$pageTitle = "Login";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - BuscaPet</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="login-container">
            <div class="login-card shadow-custom">
                <div class="login-header">
                    <i class="fas fa-paw"></i>
                    <h2>BuscaPet</h2>
                    <p class="text-muted">Faça login para continuar</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="/login.php" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user"></i> Usuário ou E-mail
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                               placeholder="Digite seu usuário ou e-mail"
                               required>
                        <div class="invalid-feedback">
                            Por favor, informe seu usuário ou e-mail.
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Senha
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Digite sua senha"
                               required>
                        <div class="invalid-feedback">
                            Por favor, informe sua senha.
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 btn-loading">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </button>
                </form>
                
                <hr>
                
                <div class="text-center">
                    <a href="/" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar ao Início
                    </a>
                </div>
                
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        Para testar o sistema, use:<br>
                        <strong>Usuário:</strong> admin<br>
                        <strong>Senha:</strong> admin123
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="/assets/js/main.js"></script>
</body>
</html>