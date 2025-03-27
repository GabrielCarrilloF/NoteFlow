<?php
class User {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Método de login (ya existente)
    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT ID, Password, User FROM authentication WHERE User = :user LIMIT 1");
        $stmt->bindParam(":user", $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $password === $user['Password']) { // Texto plano para simplificar, pero se recomienda encriptar
            return $user;
        }
        return false;
    }
    
    // Método de registro (ya existente)
    public function register($username, $password, $fullName, $email) {
        $stmt = $this->pdo->prepare("INSERT INTO authentication (User, Password) VALUES (:user, :password)");
        $stmt->bindParam(":user", $username);
        $stmt->bindParam(":password", $password);
        if($stmt->execute()){
            $authId = $this->pdo->lastInsertId();
            $stmt2 = $this->pdo->prepare("INSERT INTO user_information (AuthID, FullName, Email) VALUES (:authId, :fullName, :email)");
            $stmt2->bindParam(":authId", $authId);
            $stmt2->bindParam(":fullName", $fullName);
            $stmt2->bindParam(":email", $email);
            return $stmt2->execute();
        }
        return false;
    }
    
    // Obtiene el perfil del usuario (información de ambas tablas)
    public function getProfile($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT auth.User, ui.FullName, ui.Email 
            FROM authentication AS auth 
            INNER JOIN user_information AS ui ON auth.ID = ui.AuthID 
            WHERE auth.ID = :user_id
        ");
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Actualiza la información del perfil
    public function updateProfile($user_id, $fullName, $email) {
        $stmt = $this->pdo->prepare("
            UPDATE user_information 
            SET FullName = :fullName, Email = :email 
            WHERE AuthID = :user_id
        ");
        $stmt->bindParam(":fullName", $fullName);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
