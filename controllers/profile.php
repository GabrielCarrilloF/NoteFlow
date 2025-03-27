<?php
session_start();
require_once "../config/database.php";
require_once "../models/User.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit();
}

$userModel = new User($pdo);
$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Procesar actualización del perfil
    $fullName = trim($_POST["fullName"]);
    $email = trim($_POST["email"]);
    
    if (empty($fullName) || empty($email)) {
        $_SESSION["error"] = "All fields are required.";
        header("Location: ../views/profile.php");
        exit();
    }
    
    $updated = $userModel->updateProfile($user_id, $fullName, $email);
    if ($updated) {
        $_SESSION["success"] = "Profile updated successfully.";
    } else {
        $_SESSION["error"] = "Profile update failed.";
    }
    header("Location: ../views/profile.php");
    exit();
} else {
    // Para solicitudes GET, incluir la vista
    include "../views/profile.php";
    exit();
}
?>
