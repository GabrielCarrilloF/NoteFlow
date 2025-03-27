<?php
session_start();
require_once "../config/database.php";
require_once "../models/User.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    $userModel = new User($pdo);
    $user = $userModel->login($username, $password);
    
    if($user){
        $_SESSION["user_id"] = $user["ID"];
        $_SESSION["user_name"] = $user["User"];
        header("Location: ../views/dashboard.php");
        exit();
    } else {
        $_SESSION["error"] = "Invalid credentials.";
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
