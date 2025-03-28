<?php
session_start();
require_once "../config/database.php";
require_once "../models/User.php";

$token = $_GET['token'] ?? '';
$error = '';
$validToken = false;

if($token) {
    $userModel = new User($pdo);
    $tokenData = $userModel->validatePasswordResetToken($token);
    
    if($tokenData) {
        $validToken = true;
        $userId = $tokenData['user_id'];
    } else {
        $error = "El enlace de recuperación no es válido o ha expirado.";
    }
}

if($_SERVER["REQUEST_METHOD"] === "POST" && $validToken) {
    $password = trim($_POST["password"]);
    $confirmPassword = trim($_POST["confirm_password"]);
    
    if($password === $confirmPassword) {
        if($userModel->updatePassword($userId, $password)) {
            $userModel->deletePasswordResetToken($userId);
            $_SESSION['success'] = "Tu contraseña ha sido actualizada correctamente.";
            header("Location: ../index.php");
            exit();
        } else {
            $error = "Hubo un error al actualizar tu contraseña. Por favor, intenta nuevamente.";
        }
    } else {
        $error = "Las contraseñas no coinciden.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NoteFlow - Restablecer Contraseña</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../assets/css/forgot_password.css">
</head>
<body>
  <div class="password-reset-wrapper">
    <div class="password-reset-container">
      <div class="password-reset-header">
        <div class="logo-container">
          <img src="../assets/img/logo-app.jpg" alt="NoteFlow Logo" class="logo">
        </div>
        <h1 class="app-title">Restablecer Contraseña</h1>
      </div>
      
      <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo $error; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      
      <?php if($validToken): ?>
        <form method="POST" class="password-reset-form">
          <div class="form-group">
            <label for="password" class="form-label">
              <i class="bi bi-lock-fill"></i> Nueva Contraseña
            </label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-key"></i></span>
              <input type="password" name="password" id="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
              <button class="btn btn-outline-secondary toggle-password" type="button">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
          
          <div class="form-group">
            <label for="confirm_password" class="form-label">
              <i class="bi bi-lock-fill"></i> Confirmar Nueva Contraseña
            </label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
              <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Repite tu contraseña" required>
              <button class="btn btn-outline-secondary toggle-password" type="button">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
          
          <button type="submit" class="btn btn-primary w-100 reset-btn">
            <i class="bi bi-check-circle"></i> Actualizar Contraseña
          </button>
        </form>
      <?php else: ?>
        <div class="alert alert-warning">
          <?php echo $error ? $error : "Token no proporcionado."; ?>
        </div>
        <div class="text-center mt-3">
          <a href="../views/forgot_password.php" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Solicitar nuevo enlace
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
      button.addEventListener('click', function() {
        const input = this.closest('.input-group').querySelector('input');
        const icon = this.querySelector('i');
        
        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.remove('bi-eye');
          icon.classList.add('bi-eye-slash');
        } else {
          input.type = 'password';
          icon.classList.remove('bi-eye-slash');
          icon.classList.add('bi-eye');
        }
      });
    });
  </script>
</body>
</html>