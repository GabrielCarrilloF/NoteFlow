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
  <title>NoteFlow - Registro</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>
  <div class="register-wrapper">
    <div class="register-container">
      <div class="register-header">
        <div class="logo-container">
          <img src="assets/img/logo app.jpg" alt="NoteFlow Logo" class="logo">
        </div>
        <h1 class="app-title">Crear Cuenta</h1>
        <p class="app-subtitle">Únete a NoteFlow y organiza tus tareas eficientemente</p>
      </div>
      
      <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo $_SESSION['error']; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php unset($_SESSION['error']); endif; ?>
      
      <form action="controllers/register.php" method="POST" class="register-form">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="fullName" class="form-label">
                <i class="bi bi-person-badge"></i> Nombre Completo
              </label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" name="fullName" id="fullName" class="form-control" placeholder="Ej: Juan Pérez" required>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-group">
              <label for="username" class="form-label">
                <i class="bi bi-person-circle"></i> Nombre de Usuario
              </label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-at"></i></span>
                <input type="text" name="username" id="username" class="form-control" placeholder="Ej: juanperez" required>
              </div>
              <small class="form-text text-muted">Mínimo 4 caracteres</small>
            </div>
          </div>
        </div>
        
        <div class="form-group">
          <label for="email" class="form-label">
            <i class="bi bi-envelope-fill"></i> Correo Electrónico
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" id="email" class="form-control" placeholder="tu@email.com" required>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="password" class="form-label">
                <i class="bi bi-lock-fill"></i> Contraseña
              </label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-key"></i></span>
                <input type="password" name="password" id="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
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
          </div>
          
          <div class="col-md-6">
            <div class="form-group">
              <label for="confirmPassword" class="form-label">
                <i class="bi bi-lock-fill"></i> Confirmar Contraseña
              </label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" placeholder="Repite tu contraseña" required>
                <button class="btn btn-outline-secondary toggle-password" type="button">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              <small id="passwordMatch" class="form-text"></small>
            </div>
          </div>
        </div>
        
        <div class="form-group form-check mt-3">
          <input type="checkbox" class="form-check-input" id="termsCheck" required>
          <label class="form-check-label" for="termsCheck">
            Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Términos y Condiciones</a> y la 
            <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Política de Privacidad</a>
          </label>
        </div>
        
        <button type="submit" class="btn btn-primary w-100 register-btn">
          <i class="bi bi-person-plus"></i> Registrarse
        </button>
       
        
        <div class="login-link text-center mt-3">
          ¿Ya tienes una cuenta? <a href="index.php">Inicia sesión aquí</a>
        </div>
      </form>
    </div>
    
    <!-- Modal Términos y Condiciones -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Términos y Condiciones</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p><strong>1. Aceptación de los Términos</strong><br>
            Al acceder y utilizar la aplicación NoteFlow, usted acepta cumplir con estos Términos y Condiciones y todas las leyes y regulaciones aplicables. Si no está de acuerdo con alguno de estos términos, no debe utilizar la aplicación.</p>
            
            <p><strong>2. Uso de la Aplicación</strong><br>
            NoteFlow le otorga una licencia limitada, no exclusiva e intransferible para utilizar la aplicación únicamente para su uso personal y no comercial. Usted se compromete a no utilizar la aplicación para ningún propósito ilegal o prohibido por estos términos.</p>
            
            <p><strong>3. Registro de Usuario</strong><br>
            Para acceder a ciertas funciones de la aplicación, es posible que deba registrarse y crear una cuenta. Usted es responsable de mantener la confidencialidad de su información de cuenta y de todas las actividades que ocurran bajo su cuenta.</p>
            
            <p><strong>4. Contenido del Usuario</strong><br>
            Usted conserva todos los derechos sobre el contenido que cree y almacene en NoteFlow. Sin embargo, al subir contenido a la aplicación, otorga a NoteFlow una licencia para utilizar, almacenar y procesar dicho contenido con el fin de proporcionar los servicios de la aplicación.</p>
            
            <p><strong>5. Privacidad</strong><br>
            Su privacidad es importante para nosotros. Nuestra Política de Privacidad explica cómo recopilamos, utilizamos y protegemos su información personal. Al utilizar NoteFlow, usted acepta las prácticas descritas en la Política de Privacidad.</p>
            
            <p><strong>6. Modificaciones</strong><br>
            NoteFlow se reserva el derecho de modificar estos Términos y Condiciones en cualquier momento. Las modificaciones entrarán en vigor inmediatamente después de su publicación en la aplicación. Es su responsabilidad revisar periódicamente los términos para estar al tanto de cualquier cambio.</p>
            
            <p><strong>7. Terminación</strong><br>
            Podemos suspender o terminar su acceso a NoteFlow en cualquier momento, sin previo aviso, si incumple estos Términos y Condiciones o por cualquier otra razón que consideremos apropiada.</p>
            
            <p><strong>8. Limitación de Responsabilidad</strong><br>
            NoteFlow se proporciona "tal cual" y "según disponibilidad". No garantizamos que la aplicación sea ininterrumpida o libre de errores. En ningún caso, NoteFlow será responsable por daños indirectos, incidentales o consecuentes que resulten del uso o la imposibilidad de usar la aplicación.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal Política de Privacidad -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Política de Privacidad</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
           <p><strong>1. Información que Recopilamos</strong><br>
              Recopilamos información que usted nos proporciona directamente, como al crear una cuenta, y automáticamente, como datos de uso y dispositivos.
            </p>
            <p><strong>2. Uso de la Información</strong><br>
              Utilizamos su información para proporcionar, mantener y mejorar nuestros servicios, comunicarnos con usted y garantizar la seguridad de la aplicación.
            </p>
            <p><strong>3. Compartir Información</strong><br>
              No compartimos su información personal con terceros, excepto cuando sea necesario para proporcionar nuestros servicios, cumplir con la ley o proteger nuestros derechos.
            </p>
            <p><strong>4. Seguridad de la Información</strong><br>
              Implementamos medidas de seguridad para proteger su información personal contra accesos no autorizados y pérdidas.
            </p>
            <p><strong>5. Sus Derechos</strong><br>
              Usted tiene derecho a acceder, corregir o eliminar su información personal. Puede hacerlo a través de la configuración de su cuenta o contactándonos directamente.
            </p>
            <p><strong>6. Cambios en la Política de Privacidad</strong><br>
              Podemos actualizar esta Política de Privacidad ocasionalmente. Le notificaremos sobre cambios significativos y le animamos a revisar la política periódicamente.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
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
    const passwordInput = document.getElementById('password');
    const strengthBar = document.querySelector('.progress-bar');
    const strengthText = document.querySelector('.password-strength-text span');
    
    passwordInput.addEventListener('input', function() {
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

    // Password match verification
    const confirmPassword = document.getElementById('confirmPassword');
    const passwordMatch = document.getElementById('passwordMatch');
    
    confirmPassword.addEventListener('input', function() {
      if (this.value !== passwordInput.value) {
        passwordMatch.textContent = 'Las contraseñas no coinciden';
        passwordMatch.style.color = 'var(--bs-danger)';
      } else {
        passwordMatch.textContent = 'Las contraseñas coinciden';
        passwordMatch.style.color = 'var(--bs-success)';
      }
    });
  </script>
</body>
</html>