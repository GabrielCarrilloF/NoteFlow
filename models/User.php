<?php
class User {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Iniciar sesión: se busca el usuario y se compara la contraseña (texto plano en este ejemplo)
    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT ID, Password, User FROM authentication WHERE User = :user LIMIT 1");
        $stmt->bindParam(":user", $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $password === $user['Password']) {
            return $user;
        }
        return false;
    }
    
    // Registro: Inserta en la tabla de autenticación y en la tabla de información del usuario
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
}
?>
