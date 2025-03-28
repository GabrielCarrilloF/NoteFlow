<?php
session_start();
require_once "../config/database.php";
require_once "../models/User.php";

$userModel = new User($pdo);
$step = $_GET['step'] ?? 1;

// Paso 1: Verificar usuario
if ($step == 1 && $_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $user = $userModel->getUserByUsername($username);
    
    if ($user) {
        $_SESSION['recovery_user_id'] = $user['ID'];
        header("Location: ../views/password_recovery.php?step=2");
        exit();
    } else {
        $_SESSION['error'] = "Usuario no encontrado";
        header("Location: ../views/password_recovery.php");
        exit();
    }
}

// Paso 2: Verificar respuesta de seguridad
if ($step == 2 && $_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['recovery_user_id'])) {
        header("Location: ../views/password_recovery.php");
        exit();
    }
    
    $answer = trim($_POST["security_answer"]);
    $userId = $_SESSION['recovery_user_id'];
    
    if ($userModel->verifySecurityAnswer($userId, $answer)) {
        $_SESSION['recovery_verified'] = true;
        header("Location: ../views/password_recovery.php?step=3");
        exit();
    } else {
        $_SESSION['error'] = "Respuesta de seguridad incorrecta";
        header("Location: ../views/password_recovery.php?step=2");
        exit();
    }
}

// Paso 3: Actualizar contraseña
if ($step == 3 && $_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['recovery_user_id']) || !isset($_SESSION['recovery_verified'])) {
        header("Location: ../views/password_recovery.php");
        exit();
    }
    
    $newPassword = trim($_POST["new_password"]);
    $confirmPassword = trim($_POST["confirm_password"]);
    $userId = $_SESSION['recovery_user_id'];
    
    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "Las contraseñas no coinciden";
        header("Location: ../views/password_recovery.php?step=3");
        exit();
    }
    
    if ($userModel->updatePasswordDirectly($userId, $newPassword)) {
        unset($_SESSION['recovery_user_id']);
        unset($_SESSION['recovery_verified']);
        $_SESSION['success'] = "Contraseña actualizada correctamente";
        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['error'] = "Error al actualizar la contraseña";
        header("Location: ../views/password_recovery.php?step=3");
        exit();
    }
}