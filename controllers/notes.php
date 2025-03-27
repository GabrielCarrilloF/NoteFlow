<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);

    if (!empty($title) && !empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO notes (UserID, Title, Content) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $title, $content]);
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "All fields are required."]);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = $pdo->prepare("SELECT ID, Title, Content FROM notes WHERE UserID = ?");
    $stmt->execute([$user_id]);
    echo json_encode($stmt->fetchAll());
}
?>
