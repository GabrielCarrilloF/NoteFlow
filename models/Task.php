<?php
class Task {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getTasks($userId) {
        $stmt = $this->pdo->prepare("SELECT NoteID, Title, Content, CreatedAt FROM notes WHERE UserID = :userId ORDER BY CreatedAt DESC");
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addTask($userId, $title, $content) {
        $stmt = $this->pdo->prepare("INSERT INTO notes (UserID, Title, Content, CreatedAt) VALUES (:userId, :title, :content, NOW())");
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":content", $content);
        return $stmt->execute();
    }
    
    public function updateTask($taskId, $userId, $title, $content) {
        $stmt = $this->pdo->prepare("UPDATE notes SET Title = :title, Content = :content WHERE NoteID = :taskId AND UserID = :userId");
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":taskId", $taskId, PDO::PARAM_INT);
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function deleteTask($taskId, $userId) {
        $stmt = $this->pdo->prepare("DELETE FROM notes WHERE NoteID = :taskId AND UserID = :userId");
        $stmt->bindParam(":taskId", $taskId, PDO::PARAM_INT);
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
