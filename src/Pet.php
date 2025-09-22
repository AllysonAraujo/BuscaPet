<?php
require_once '../config/database.php';

class Pet {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function create($name, $species, $breed, $age, $description, $image_url, $user_id) {
        try {
            $query = "INSERT INTO pets (name, species, breed, age, description, image_url, user_id) 
                     VALUES (:name, :species, :breed, :age, :description, :image_url, :user_id)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':species', $species);
            $stmt->bindParam(':breed', $breed);
            $stmt->bindParam(':age', $age);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':image_url', $image_url);
            $stmt->bindParam(':user_id', $user_id);

            if ($stmt->execute()) {
                $this->logActivity($user_id, 'pet_created', "Pet created: $name");
                return ['success' => true, 'message' => 'Pet cadastrado com sucesso', 'pet_id' => $this->db->lastInsertId()];
            }

        } catch (PDOException $exception) {
            return ['success' => false, 'message' => 'Erro ao cadastrar pet: ' . $exception->getMessage()];
        }

        return ['success' => false, 'message' => 'Erro desconhecido'];
    }

    public function getAll($limit = 10, $offset = 0, $search = '', $species_filter = '') {
        try {
            $where_conditions = [];
            $params = [];

            if (!empty($search)) {
                $where_conditions[] = "(name LIKE :search OR breed LIKE :search OR description LIKE :search)";
                $params[':search'] = '%' . $search . '%';
            }

            if (!empty($species_filter)) {
                $where_conditions[] = "species = :species";
                $params[':species'] = $species_filter;
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            $query = "SELECT p.*, u.username as owner_username 
                     FROM pets p 
                     JOIN users u ON p.user_id = u.id 
                     $where_clause 
                     ORDER BY p.created_at DESC 
                     LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $exception) {
            error_log("Error fetching pets: " . $exception->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $query = "SELECT p.*, u.username as owner_username 
                     FROM pets p 
                     JOIN users u ON p.user_id = u.id 
                     WHERE p.id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch();

        } catch (PDOException $exception) {
            return null;
        }
    }

    public function update($id, $name, $species, $breed, $age, $description, $image_url, $user_id) {
        try {
            // Check if user owns this pet
            $query = "SELECT id FROM pets WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Pet não encontrado ou você não tem permissão'];
            }

            $query = "UPDATE pets SET name = :name, species = :species, breed = :breed, 
                     age = :age, description = :description, image_url = :image_url 
                     WHERE id = :id AND user_id = :user_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':species', $species);
            $stmt->bindParam(':breed', $breed);
            $stmt->bindParam(':age', $age);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':image_url', $image_url);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);

            if ($stmt->execute()) {
                $this->logActivity($user_id, 'pet_updated', "Pet updated: $name (ID: $id)");
                return ['success' => true, 'message' => 'Pet atualizado com sucesso'];
            }

        } catch (PDOException $exception) {
            return ['success' => false, 'message' => 'Erro ao atualizar pet: ' . $exception->getMessage()];
        }

        return ['success' => false, 'message' => 'Erro desconhecido'];
    }

    public function delete($id, $user_id) {
        try {
            $query = "DELETE FROM pets WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);

            if ($stmt->execute() && $stmt->rowCount() > 0) {
                $this->logActivity($user_id, 'pet_deleted', "Pet deleted (ID: $id)");
                return ['success' => true, 'message' => 'Pet removido com sucesso'];
            }

            return ['success' => false, 'message' => 'Pet não encontrado ou você não tem permissão'];

        } catch (PDOException $exception) {
            return ['success' => false, 'message' => 'Erro ao remover pet: ' . $exception->getMessage()];
        }
    }

    public function getCount($search = '', $species_filter = '') {
        try {
            $where_conditions = [];
            $params = [];

            if (!empty($search)) {
                $where_conditions[] = "(name LIKE :search OR breed LIKE :search OR description LIKE :search)";
                $params[':search'] = '%' . $search . '%';
            }

            if (!empty($species_filter)) {
                $where_conditions[] = "species = :species";
                $params[':species'] = $species_filter;
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            $query = "SELECT COUNT(*) as total FROM pets $where_clause";
            $stmt = $this->db->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['total'];

        } catch (PDOException $exception) {
            return 0;
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