<?php
session_start();
require_once "../config/database.php";
require_once "../models/Task.php";

if(!isset($_SESSION["user_id"])){
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$taskModel = new Task($pdo);
$userId = $_SESSION["user_id"];

$method = $_SERVER["REQUEST_METHOD"];

if($method === "GET"){
    $tasks = $taskModel->getTasks($userId);
    echo json_encode($tasks);
    exit();
} elseif($method === "POST"){
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    if(empty($title) || empty($content)){
        echo json_encode(["error" => "All fields are required"]);
        exit();
    }
    $result = $taskModel->addTask($userId, $title, $content);
    echo json_encode(["success" => $result]);
    exit();
} elseif($method === "PUT"){
    parse_str(file_get_contents("php://input"), $_PUT);
    $taskId = $_PUT["taskId"];
    $title = trim($_PUT["title"]);
    $content = trim($_PUT["content"]);
    $result = $taskModel->updateTask($taskId, $userId, $title, $content);
    echo json_encode(["success" => $result]);
    exit();
} elseif($method === "DELETE"){
    parse_str(file_get_contents("php://input"), $_DELETE);
    $taskId = $_DELETE["taskId"];
    $result = $taskModel->deleteTask($taskId, $userId);
    echo json_encode(["success" => $result]);
    exit();
} else {
    echo json_encode(["error" => "Invalid request"]);
    exit();
}
?>
