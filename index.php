<?php
session_start();
if(isset($_SESSION['user_id'])){
    header("Location: views/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NoteFlow - Iniciar Sesión</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
  <div class="login-wrapper">
    <div class="login-container">
      <div class="login-header">
        <div class="logo-container">
          <img src="assets/img/logo app.jpg" alt="NoteFlow Logo" class="logo">
        </div>
        <h1 class="app-title">NoteFlow</h1>
        <p class="app-subtitle">Organiza tus tareas de manera eficiente</p>
      </div>
      
      <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo $_SESSION['error']; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php unset($_SESSION['error']); endif; ?>
      
      <form action="controllers/login.php" method="POST" class="login-form">
        <div class="form-group">
          <label for="username" class="form-label">
            <i class="bi bi-person-fill"></i> Nombre de usuario
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="username" id="username" class="form-control" placeholder="Ingresa tu usuario" required>
          </div>
        </div>
        
        <div class="form-group">
          <label for="password" class="form-label">
            <i class="bi bi-lock-fill"></i> Contraseña
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="password" class="form-control" placeholder="Ingresa tu contraseña" required>
            <button class="btn btn-outline-secondary toggle-password" type="button">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe">
            <label class="form-check-label" for="rememberMe">Recordarme</label>
          </div>
          <a href="views/password_recovery.php" class="forgot-password">¿Olvidaste tu contraseña?</a>
        </div>
        
        <button type="submit" class="btn btn-primary w-100 login-btn">
          <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
        </button>
        
        
        <div class="register-link text-center mt-3">
          ¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a>
        </div>
      </form>
    </div>
    
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle password visibility
    document.querySelector('.toggle-password').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const icon = this.querySelector('i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
  </script>
</body>
</html>