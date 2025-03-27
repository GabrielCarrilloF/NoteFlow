<?php
session_start();
require_once "../config/database.php";
require_once "../models/User.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $fullName = trim($_POST["fullName"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    if(empty($fullName) || empty($username) || empty($email) || empty($password)){
        $_SESSION["error"] = "All fields are required.";
        header("Location: ../register.php");
        exit();
    }
    
    $userModel = new User($pdo);
    $registered = $userModel->register($username, $password, $fullName, $email);
    
    if($registered){
        // Tras el registro se inicia sesión (en un sistema real, recuperar el ID correctamente)
        $_SESSION["user_id"] = $pdo->lastInsertId();
        $_SESSION["user_name"] = $username;
        header("Location: ../views/dashboard.php");
        exit();
    } else {
        $_SESSION["error"] = "Registration failed.";
        header("Location: ../register.php");
        exit();
    }
} else {
    header("Location: ../register.php");
    exit();
}
?>
