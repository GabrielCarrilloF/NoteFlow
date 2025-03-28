<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit();
}
require_once "../config/database.php";
require_once "../models/User.php";

$userModel = new User($pdo);
$user_id = $_SESSION["user_id"];
$profile = $userModel->getProfile($user_id);

if (!$profile) {
    $_SESSION['error'] = "Error: Profile not found. Please contact support.";
    header("Location: dashboard.php");
    exit();
}

$firstName = explode(" ", $profile["FullName"])[0];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NoteFlow - Perfil</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../assets/css/perfil.css">
</head>
<body class="dashboard-body">
  <!-- Sidebar Navigation -->
  <div class="dashboard-container">
    <aside class="dashboard-sidebar">
      <div class="sidebar-header">
        <h2 class="brand-text">NoteFlow</h2>
        <div class="user-welcome">
          <div class="avatar-placeholder profile-avatar">
            <?php echo strtoupper(substr($firstName, 0, 1)); ?>
          </div>
          <div>
            <p class="welcome-text"><?php echo htmlspecialchars($firstName); ?></p>
            <small class="text-muted">Mi perfil</small>
          </div>
        </div>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li><a href="dashboard.php"><i class="bi bi-check-circle"></i> Mis Tareas</a></li>
          <li class="active"><a href="#"><i class="bi bi-person"></i> Perfil</a></li>
          <li><a href="../controllers/logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
        </ul>
      </nav>
      <div class="sidebar-footer">
        <button class="btn btn-primary new-task-btn" data-bs-toggle="modal" data-bs-target="#taskModal">
          <i class="bi bi-plus-lg"></i> Nueva Tarea
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-content">
      <header class="content-header">
        <h1><i class="bi bi-person"></i> Mi Perfil</h1>
      </header>

      <?php if(isset($_SESSION["success"])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?php echo $_SESSION["success"]; unset($_SESSION["success"]); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      
      <?php if(isset($_SESSION["error"])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="profile-container">
        <div class="row">
          <div class="col-lg-4">
            <div class="profile-card">
              <div class="profile-avatar-large">
                <?php echo strtoupper(substr($firstName, 0, 1)); ?>
              </div>
              <h3 class="profile-name"><?php echo htmlspecialchars($profile["FullName"]); ?></h3>
              <p class="profile-username">@<?php echo htmlspecialchars($profile["User"]); ?></p>
              <div class="profile-stats">
                <div class="stat-item">
                  <i class="bi bi-calendar-check"></i>
                  <span>Miembro desde: <?php echo date('M Y', strtotime($profile["created_at"] ?? 'now')); ?></span>
                </div>
                <div class="stat-item">
                  <i class="bi bi-pencil-square"></i>
                  <span>Última actualización: <?php echo date('d M Y', strtotime($profile["updated_at"] ?? 'now')); ?></span>
                </div>
              </div>
              <hr>
              <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="bi bi-key"></i> Cambiar Contraseña
              </button>
            </div>
          </div>
          
          <div class="col-lg-8">
            <div class="profile-edit-card">
              <h4><i class="bi bi-pencil-square"></i> Editar Información</h4>
              <form action="../controllers/profile.php" method="POST">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="fullName" class="form-label">Nombre Completo</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="fullName" id="fullName" class="form-control" 
                               value="<?php echo htmlspecialchars($profile["FullName"]); ?>" required>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="email" class="form-label">Correo Electrónico</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control" 
                               value="<?php echo htmlspecialchars($profile["Email"]); ?>" required>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="username" class="form-label">Nombre de Usuario</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-at"></i></span>
                    <input type="text" name="username" id="username" class="form-control" 
                           value="<?php echo htmlspecialchars($profile["User"]); ?>" readonly>
                  </div>
                  <small class="text-muted">El nombre de usuario no puede ser cambiado</small>
                </div>
                
                <div class="mb-3">
                  <label for="bio" class="form-label">Biografía</label>
                  <textarea name="bio" id="bio" class="form-control" rows="3" 
                            placeholder="Cuéntanos algo sobre ti"><?php echo htmlspecialchars($profile["bio"] ?? ''); ?></textarea>
                </div>
                
                <div class="text-end">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar Cambios
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Change Password Modal -->
  <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-key"></i> Cambiar Contraseña</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="../controllers/change_password.php" method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <label for="currentPassword" class="form-label">Contraseña Actual</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="currentPassword" id="currentPassword" class="form-control" required>
                <button class="btn btn-outline-secondary toggle-password" type="button">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="newPassword" class="form-label">Nueva Contraseña</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-key"></i></span>
                <input type="password" name="newPassword" id="newPassword" class="form-control" required>
                <button class="btn btn-outline-secondary toggle-password" type="button">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              <div class="password-strength mt-2">
                <div class="progress">
                  <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
                <small class="password-strength-text">Seguridad: <span>Débil</span></small>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="confirmPassword" class="form-label">Confirmar Nueva Contraseña</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" required>
                <button class="btn btn-outline-secondary toggle-password" type="button">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              <small id="passwordMatch" class="form-text"></small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/zxcvbn@4.4.2/dist/zxcvbn.js"></script>
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

    // Password strength meter
    const newPassword = document.getElementById('newPassword');
    const strengthBar = document.querySelector('#changePasswordModal .progress-bar');
    const strengthText = document.querySelector('#changePasswordModal .password-strength-text span');
    
    if (newPassword) {
      newPassword.addEventListener('input', function() {
        const result = zxcvbn(this.value);
        const width = (result.score * 25) + 25;
        let text = '';
        let color = '';
        
        switch(result.score) {
          case 0:
          case 1:
            text = 'Débil';
            color = 'bg-danger';
            break;
          case 2:
            text = 'Moderada';
            color = 'bg-warning';
            break;
          case 3:
            text = 'Fuerte';
            color = 'bg-info';
            break;
          case 4:
            text = 'Muy fuerte';
            color = 'bg-success';
            break;
        }
        
        strengthBar.style.width = width + '%';
        strengthBar.className = `progress-bar ${color}`;
        strengthText.textContent = text;
      });
    }

    // Password match verification
    const confirmPassword = document.getElementById('confirmPassword');
    const passwordMatch = document.getElementById('passwordMatch');
    
    if (confirmPassword) {
      confirmPassword.addEventListener('input', function() {
        if (this.value !== newPassword.value) {
          passwordMatch.textContent = 'Las contraseñas no coinciden';
          passwordMatch.style.color = 'var(--bs-danger)';
        } else {
          passwordMatch.textContent = 'Las contraseñas coinciden';
          passwordMatch.style.color = 'var(--bs-success)';
        }
      });
    }
  </script>
</body>
</html>