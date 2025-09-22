<?php
/**
 * Database Setup Script
 * Run this file to initialize the database and create the default admin user
 */

require_once 'config/database.php';

echo "<h1>BuscaPet - Database Setup</h1>";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "<p style='color: green;'>✓ Conexão com o banco estabelecida com sucesso!</p>";
        
        // Create tables
        echo "<h2>Criando tabelas...</h2>";
        $database->createTables();
        echo "<p style='color: green;'>✓ Tabelas criadas com sucesso!</p>";
        
        // Create default admin user
        echo "<h2>Criando usuário administrador...</h2>";
        createDefaultAdmin($database);
        echo "<p style='color: green;'>✓ Usuário administrador criado!</p>";
        
        echo "<hr>";
        echo "<h2>Setup concluído com sucesso!</h2>";
        echo "<p>Você pode fazer login com:</p>";
        echo "<ul>";
        echo "<li><strong>Usuário:</strong> admin</li>";
        echo "<li><strong>Senha:</strong> admin123</li>";
        echo "</ul>";
        echo "<p><a href='/login.php' style='color: blue; text-decoration: none;'>→ Fazer Login</a></p>";
        echo "<p><a href='/index.php' style='color: blue; text-decoration: none;'>→ Voltar ao Início</a></p>";
        
    } else {
        echo "<p style='color: red;'>✗ Erro na conexão com o banco de dados!</p>";
        echo "<p>Verifique as configurações em config/database.php</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro: " . $e->getMessage() . "</p>";
    echo "<h3>Instruções para configuração do banco:</h3>";
    echo "<ol>";
    echo "<li>Certifique-se de que o MySQL está rodando</li>";
    echo "<li>Crie um banco de dados chamado 'buscapet_db'</li>";
    echo "<li>Ajuste as configurações em config/database.php se necessário</li>";
    echo "<li>Execute este script novamente</li>";
    echo "</ol>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background-color: #f8f9fa;
}
h1 {
    color: #0d6efd;
    text-align: center;
}
h2 {
    color: #333;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 10px;
}
</style>