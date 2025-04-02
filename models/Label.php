<?php
class Label {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getLabels($userId) {
        $stmt = $this->pdo->prepare("
            SELECT ID, name, color 
            FROM labels 
            WHERE UserID = :userId OR is_global = TRUE
            ORDER BY is_global DESC, name ASC
        ");
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addLabel($userId, $name, $color, $isGlobal = false) {
        $stmt = $this->pdo->prepare("
            INSERT INTO labels (name, color, UserID, is_global, created_at, updated_at) 
            VALUES (:name, :color, :userId, :isGlobal, NOW(), NOW())
        ");
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":color", $color);
        $stmt->bindParam(":userId", $userId, $isGlobal ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(":isGlobal", $isGlobal, PDO::PARAM_BOOL);
        return $stmt->execute();
    }
    
    public function updateLabel($id, $userId, $name, $color) {
        $stmt = $this->pdo->prepare("
            UPDATE labels 
            SET name = :name, color = :color, updated_at = NOW() 
            WHERE ID = :id AND (UserID = :userId OR is_global = TRUE)
        ");
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":color", $color);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function deleteLabel($id, $userId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM labels 
            WHERE ID = :id AND UserID = :userId AND is_global = FALSE
        ");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function getLabelById($id, $userId) {
        $stmt = $this->pdo->prepare("
            SELECT ID, name, color, is_global 
            FROM labels 
            WHERE ID = :id AND (UserID = :userId OR is_global = TRUE)
        ");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>