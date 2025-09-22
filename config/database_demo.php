<?php
// Mock database for demonstration when MySQL is not available
class MockDatabase {
    public function getConnection() {
        return null; // Simulate no database connection
    }
}

class Database {
    private $host = 'localhost';
    private $db_name = 'buscapet';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            // Return mock database for demonstration
            error_log("Database connection failed, using mock data: " . $exception->getMessage());
            return null;
        }

        return $this->conn;
    }
}
?>