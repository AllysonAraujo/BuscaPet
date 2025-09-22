<?php
/**
 * Database Configuration
 * MySQL connection settings for BuscaPet application
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'buscapet_db';
    private $username = 'root';
    private $password = '';
    private $connection;

    public function getConnection() {
        $this->connection = null;
        
        try {
            $this->connection = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Erro na conexão: " . $exception->getMessage();
        }
        
        return $this->connection;
    }
    
    /**
     * Create database tables if they don't exist
     */
    public function createTables() {
        $queries = [
            // Users table
            "CREATE TABLE IF NOT EXISTS users (
                id INT PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )",
            
            // Pets table for internal management
            "CREATE TABLE IF NOT EXISTS pets (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                species VARCHAR(50) NOT NULL,
                breed VARCHAR(100),
                age INT,
                description TEXT,
                status VARCHAR(20) DEFAULT 'available',
                created_by INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (created_by) REFERENCES users(id)
            )"
        ];
        
        foreach ($queries as $query) {
            try {
                $this->connection->exec($query);
            } catch(PDOException $exception) {
                echo "Erro ao criar tabela: " . $exception->getMessage();
            }
        }
    }
}

// Create default admin user function
function createDefaultAdmin($database) {
    $conn = $database->getConnection();
    
    // Check if admin user exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    
    if ($stmt->fetchColumn() == 0) {
        // Create default admin user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt->execute(['admin', 'admin@buscapet.com', $hashedPassword]);
    }
}
?>