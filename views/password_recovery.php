<?php
session_start();
require_once "../config/database.php";
require_once "../models/User.php"; // Asegúrate que esta ruta es correcta

$step = $_GET['step'] ?? 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña - NoteFlow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .recovery-container { max-width: 500px; margin: 50px auto; }
    </style>
</head>
<body>
    <div class="container recovery-container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="text-center">Recuperación de Contraseña</h3>
            </div>
            
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                
                <?php if ($step == 1): ?>
                    <form action="../controllers/password_recovery.php?step=1" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Continuar</button>
                    </form>
                
                <?php elseif ($step == 2 && isset($_SESSION['recovery_user_id'])): 
                    $userModel = new User($pdo);
                    $user = $userModel->getProfile($_SESSION['recovery_user_id']);
                ?>
                    <form action="../controllers/password_recovery.php?step=2" method="POST">
                        <div class="mb-3">
                            <p class="fw-bold">Pregunta de seguridad:</p>
                            <p><?= htmlspecialchars($user['security_question'] ?? '¿Cuál es tu color favorito?'); ?></p>
                        </div>
                        <div class="mb-3">
                            <label for="security_answer" class="form-label">Respuesta</label>
                            <input type="text" class="form-control" id="security_answer" name="security_answer" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Verificar</button>
                    </form>
                
                <?php elseif ($step == 3 && isset($_SESSION['recovery_verified'])): ?>
                    <form action="../controllers/password_recovery.php?step=3" method="POST">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Actualizar Contraseña</button>
                    </form>
                <?php endif; ?>
            </div>
            
            <div class="card-footer text-center">
                <a href="../index.php" class="text-decoration-none">Volver al inicio de sesión</a>
            </div>
        </div>
    </div>
</body>
</html>