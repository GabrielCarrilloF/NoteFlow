<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "No autorizado"]);
    exit();
}

$user_id = $_SESSION["user_id"];

$query = $pdo->prepare("SELECT title, content, created_at FROM notes WHERE user_id = ? ORDER BY created_at DESC");
$query->execute([$user_id]);

$notes = $query->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($notes);
?>
