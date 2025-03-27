<?php
session_start();
require_once "../config/database.php"; // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (!empty($user) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT ID, Password FROM authentication WHERE User = :user LIMIT 1");
        $stmt->bindParam(":user", $user, PDO::PARAM_STR);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData && $password === $userData["Password"]) {  // Comparación directa
            $_SESSION["user_id"] = $userData["ID"];
            $_SESSION["username"] = $user;
            header("Location: ../views/dashboard.php"); // Redirige al panel
            exit();
        } else {
            $_SESSION["error"] = "Invalid credentials.";
        }
    } else {
        $_SESSION["error"] = "All fields are required.";
    }
}

header("Location: ../index.php");
exit();
?>
