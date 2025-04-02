<?php
class Task {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getTasks($userId) {
        $stmt = $this->pdo->prepare("
            SELECT n.NoteID, n.UserID, n.Title, n.Content, n.labels_id, n.CreatedAt, n.UpdatedAt, 
                   l.name as label_name, l.color as label_color 
            FROM notes n
            LEFT JOIN labels l ON n.labels_id = l.ID AND (l.UserID = :userId OR l.is_global = TRUE)
            WHERE n.UserID = :userId 
            ORDER BY n.CreatedAt DESC
        ");
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addTask($userId, $title, $content, $labelId = null) {
        // Verificar que la etiqueta pertenezca al usuario o sea global
        if ($labelId) {
            $stmtCheck = $this->pdo->prepare("
                SELECT ID FROM labels 
                WHERE ID = :labelId AND (UserID = :userId OR is_global = TRUE)
            ");
            $stmtCheck->bindParam(":labelId", $labelId, PDO::PARAM_INT);
            $stmtCheck->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmtCheck->execute();
            
            if (!$stmtCheck->fetch()) {
                return false; // Etiqueta no válida para este usuario
            }
        }
        
        $stmt = $this->pdo->prepare("
            INSERT INTO notes (UserID, Title, Content, labels_id, CreatedAt, UpdatedAt) 
            VALUES (:userId, :title, :content, :labelId, NOW(), NOW())
        ");
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":labelId", $labelId, $labelId ? PDO::PARAM_INT : PDO::PARAM_NULL);
        return $stmt->execute();
    }
    
    public function updateTask($noteId, $userId, $title, $content, $labelId = null) {
        // Verificar que la etiqueta pertenezca al usuario o sea global
        if ($labelId) {
            $stmtCheck = $this->pdo->prepare("
                SELECT ID FROM labels 
                WHERE ID = :labelId AND (UserID = :userId OR is_global = TRUE)
            ");
            $stmtCheck->bindParam(":labelId", $labelId, PDO::PARAM_INT);
            $stmtCheck->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmtCheck->execute();
            
            if (!$stmtCheck->fetch()) {
                return false; // Etiqueta no válida para este usuario
            }
        }
        
        $stmt = $this->pdo->prepare("
            UPDATE notes 
            SET Title = :title, Content = :content, labels_id = :labelId, UpdatedAt = NOW() 
            WHERE NoteID = :noteId AND UserID = :userId
        ");
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":labelId", $labelId, $labelId ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindParam(":noteId", $noteId, PDO::PARAM_INT);
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // ... otros métodos permanecen igual
}
?>