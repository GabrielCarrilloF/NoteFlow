<?php
$host = "localhost";
$dbname = "NoteFlow";
$username = "root";  // Cambia según tu configuración
$password = "";      // Cambia según tu configuración

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
