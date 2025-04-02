<?php
session_start();
require_once "../config/database.php";
require_once "../models/Label.php";

if(!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$labelModel = new Label($pdo);
$userId = $_SESSION["user_id"];

$method = $_SERVER["REQUEST_METHOD"];

if($method === "GET") {
    $labels = $labelModel->getLabels($userId);
    echo json_encode($labels);
    exit();
} elseif($method === "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data["name"]);
    $color = trim($data["color"]);
    $isGlobal = isset($data["isGlobal"]) ? (bool)$data["isGlobal"] : false;
    
    if(empty($name) || empty($color)) {
        echo json_encode(["error" => "All fields are required"]);
        exit();
    }
    
    $result = $labelModel->addLabel($userId, $name, $color, $isGlobal);
    echo json_encode(["success" => $result]);
    exit();
} elseif($method === "PUT") {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data["id"];
    $name = trim($data["name"]);
    $color = trim($data["color"]);
    
    $result = $labelModel->updateLabel($id, $userId, $name, $color);
    echo json_encode(["success" => $result]);
    exit();
} elseif($method === "DELETE") {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data["id"];
    
    $result = $labelModel->deleteLabel($id, $userId);
    echo json_encode(["success" => $result]);
    exit();
} else {
    echo json_encode(["error" => "Invalid request"]);
    exit();
}
?>