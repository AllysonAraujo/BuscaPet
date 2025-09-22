<?php
require_once '../config/database.php';

class Favorite {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function add($user_id, $pet_id) {
        try {
            // Check if already favorited
            $query = "SELECT id FROM favorites WHERE user_id = :user_id AND pet_id = :pet_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':pet_id', $pet_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Pet já está nos favoritos'];
            }

            // Add to favorites
            $query = "INSERT INTO favorites (user_id, pet_id) VALUES (:user_id, :pet_id)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':pet_id', $pet_id);

            if ($stmt->execute()) {
                $this->logActivity($user_id, 'pet_favorited', "Pet favorited (ID: $pet_id)");
                return ['success' => true, 'message' => 'Pet adicionado aos favoritos'];
            }

        } catch (PDOException $exception) {
            return ['success' => false, 'message' => 'Erro ao adicionar favorito: ' . $exception->getMessage()];
        }

        return ['success' => false, 'message' => 'Erro desconhecido'];
    }

    public function remove($user_id, $pet_id) {
        try {
            $query = "DELETE FROM favorites WHERE user_id = :user_id AND pet_id = :pet_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':pet_id', $pet_id);

            if ($stmt->execute() && $stmt->rowCount() > 0) {
                $this->logActivity($user_id, 'pet_unfavorited', "Pet removed from favorites (ID: $pet_id)");
                return ['success' => true, 'message' => 'Pet removido dos favoritos'];
            }

            return ['success' => false, 'message' => 'Favorito não encontrado'];

        } catch (PDOException $exception) {
            return ['success' => false, 'message' => 'Erro ao remover favorito: ' . $exception->getMessage()];
        }
    }

    public function getUserFavorites($user_id, $limit = 10, $offset = 0) {
        try {
            $query = "SELECT p.*, u.username as owner_username, f.created_at as favorited_at
                     FROM favorites f
                     JOIN pets p ON f.pet_id = p.id
                     JOIN users u ON p.user_id = u.id
                     WHERE f.user_id = :user_id
                     ORDER BY f.created_at DESC
                     LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $exception) {
            error_log("Error fetching user favorites: " . $exception->getMessage());
            return [];
        }
    }

    public function isFavorited($user_id, $pet_id) {
        try {
            $query = "SELECT id FROM favorites WHERE user_id = :user_id AND pet_id = :pet_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':pet_id', $pet_id);
            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $exception) {
            return false;
        }
    }

    private function logActivity($user_id, $action, $details) {
        try {
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $query = "INSERT INTO logs (user_id, action, details, ip_address) VALUES (:user_id, :action, :details, :ip_address)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':ip_address', $ip_address);
            $stmt->execute();
        } catch (PDOException $exception) {
            error_log("Failed to log activity: " . $exception->getMessage());
        }
    }
}
?>