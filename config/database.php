<?php
$host = "localhost";  // Cambia si tu servidor no es local
$dbname = "NoteFlow";
$username = "root";  // Reemplázalo con tu usuario de MySQL
$password = "";      // Si tienes contraseña, agrégala aquí

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
