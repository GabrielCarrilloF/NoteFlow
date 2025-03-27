<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "No autorizado"]);
    exit();
}

$user_id = $_SESSION["user_id"];
$title = $_POST["title"] ?? "";
$content = $_POST["content"] ?? "";
$created_at = date("Y-m-d H:i:s");

if (empty($title) || empty($content)) {
    echo json_encode(["error" => "Todos los campos son obligatorios"]);
    exit();
}

$query = $pdo->prepare("INSERT INTO notes (user_id, title, content, created_at) VALUES (?, ?, ?, ?)");
$query->execute([$user_id, $title, $content, $created_at]);

echo json_encode(["success" => true]);
?>
