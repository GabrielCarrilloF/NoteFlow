<?php
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location: ../index.php");
    exit();
}
$userName = $_SESSION["user_name"];
$firstName = explode(" ", $userName)[0];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NoteFlow - Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard-body">
  <!-- Sidebar Navigation -->
  <div class="dashboard-container">
    <aside class="dashboard-sidebar">
      <div class="sidebar-header">
        <h2 class="brand-text">NoteFlow</h2>
        <div class="user-welcome">
          <div class="avatar-placeholder">
            <?php echo strtoupper(substr($firstName, 0, 1)); ?>
          </div>
          <div>
            <p class="welcome-text">Hola, <?php echo htmlspecialchars($firstName); ?></p>
            <small class="text-muted">Bienvenido de nuevo</small>
          </div>
        </div>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li class="active"><a href="dashboard.php"><i class="bi bi-check-circle"></i> Mis Tareas</a></li>
          <li><a href="profile.php"><i class="bi bi-person"></i> Perfil</a></li>
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
        <h1><i class="bi bi-check-circle"></i> Mis Tareas</h1>
        <div class="header-actions">
          <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Buscar tareas...">
          </div>
          <div class="filter-dropdown">
            <select class="form-select">
              <option>Todas</option>
              <option>Pendientes</option>
              <option>Completadas</option>
            </select>
          </div>
        </div>
      </header>

      <div class="task-stats">
        <div class="stat-card">
          <h3 id="totalTasks">0</h3>
          <p>Total</p>
        </div>
        <div class="stat-card">
          <h3 id="pendingTasks">0</h3>
          <p>Pendientes</p>
        </div>
        <div class="stat-card">
          <h3 id="completedTasks">0</h3>
          <p>Completadas</p>
        </div>
      </div>

      <div id="taskList" class="task-list">
        <!-- Las tareas se cargarán aquí -->
        <div class="empty-state">
          <i class="bi bi-check2-all"></i>
          <h3>No hay tareas aún</h3>
          <p>Crea tu primera tarea haciendo clic en el botón "Nueva Tarea"</p>
        </div>
      </div>
    </main>
  </div>

  <!-- Task Modal -->
  <div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-plus-lg"></i> Nueva Tarea</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="taskTitle" class="form-label">Título</label>
            <input type="text" id="taskTitle" class="form-control" placeholder="¿Qué necesitas hacer?">
          </div>
          <div class="mb-3">
            <label for="taskContent" class="form-label">Descripción</label>
            <textarea id="taskContent" class="form-control" rows="4" placeholder="Detalles de la tarea..."></textarea>
          </div>
          <div class="mb-3">
            <label for="taskPriority" class="form-label">Prioridad</label>
            <select id="taskPriority" class="form-select">
              <option value="low">Baja</option>
              <option value="medium" selected>Media</option>
              <option value="high">Alta</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" onclick="addTask()">Guardar Tarea</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/dashboard.js"></script>
</body>
</html>