<?php
session_start();
if(!isset($_SESSION["user_id"])) {
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
  <style>
    /* Estilos mejorados para el sidebar */
    .dashboard-sidebar {
      background-color: #2c3e50;
      color: white;
    }
    
    .label-list {
      max-height: 200px;
      overflow-y: auto;
      padding-right: 5px;
    }
    
    .label-list::-webkit-scrollbar {
      width: 5px;
    }
    
    .label-list::-webkit-scrollbar-track {
      background: #34495e;
    }
    
    .label-list::-webkit-scrollbar-thumb {
      background: #7f8c8d;
      border-radius: 10px;
    }
    
    .label-actions {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
    }
    
    /* Estilos para las tarjetas de notas */
    .note-card {
      transition: all 0.3s ease;
      border-radius: 8px;
      border-left: 4px solid #3498db;
    }
    
    .note-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .note-label {
      padding: 3px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
      color: white;
    }
    
    /* Estilos para el estado vacío */
    .empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 2rem;
    }
    
    /* Mejoras en los modales */
    .modal-content {
      border-radius: 10px;
    }
    
    .color-option {
      width: 25px;
      height: 25px;
      border-radius: 50%;
      border: 2px solid transparent;
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .color-option:hover {
      transform: scale(1.1);
    }
  </style>
</head>
<body class="dashboard-body">
  <!-- Sidebar Navigation -->
  <div class="dashboard-container">
    <aside class="dashboard-sidebar">
      <div class="sidebar-header">
        <h2 class="brand-text">NoteFlow</h2>
        <div class="user-welcome">
          <div class="avatar-placeholder bg-primary">
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
          
          <!-- Sección de Etiquetas -->
          <li class="sidebar-section">
            <div class="section-header d-flex justify-content-between align-items-center">
              <span>Etiquetas</span>
              <button class="btn btn-sm btn-link p-0 text-white" data-bs-toggle="modal" data-bs-target="#labelModal">
                <i class="bi bi-plus-lg"></i>
              </button>
            </div>
            <ul class="label-list" id="sidebarLabels">
              <!-- Las etiquetas se cargarán dinámicamente aquí -->
            </ul>
          </li>
          
          <li><a href="../controllers/logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-content">
      <header class="content-header">
        <h1><i class="bi bi-check-circle"></i> Mis Tareas</h1>
        <div class="header-actions d-flex align-items-center gap-3">
          <div class="search-box flex-grow-1">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar tareas...">
          </div>
          <button class="btn btn-primary new-task-btn" data-bs-toggle="modal" data-bs-target="#taskModal">
            <i class="bi bi-plus-lg"></i> Nueva Tarea
          </button>
        </div>
      </header>

      <div class="task-stats d-flex gap-3 my-4">
        <div class="stat-card flex-grow-1 text-center p-3 rounded-3 bg-light">
          <h3 id="totalTasks" class="mb-0">0</h3>
          <p class="mb-0 text-muted">Total</p>
        </div>
        <div class="stat-card flex-grow-1 text-center p-3 rounded-3 bg-light">
          <h3 id="pendingTasks" class="mb-0">0</h3>
          <p class="mb-0 text-muted">Pendientes</p>
        </div>
        <div class="stat-card flex-grow-1 text-center p-3 rounded-3 bg-light">
          <h3 id="completedTasks" class="mb-0">0</h3>
          <p class="mb-0 text-muted">Completadas</p>
        </div>
      </div>

      <div id="taskList" class="task-list">
        <!-- Sección de Todas las Tareas -->
        <div class="notes-section mb-5">
          <h4 class="section-title d-flex justify-content-between align-items-center mb-3">
            <span><i class="bi bi-list-check"></i> Todas las tareas</span>
            <span class="badge bg-primary rounded-pill" id="totalTasksBadge">0</span>
          </h4>
          <div class="notes-container row g-3" id="allTasks"></div>
        </div>
        
        <!-- Sección de Tareas Recientes -->
        <div class="notes-section mb-5">
          <h4 class="section-title d-flex justify-content-between align-items-center mb-3">
            <span><i class="bi bi-clock"></i> Recientes</span>
            <span class="badge bg-primary rounded-pill" id="recentCount">0</span>
          </h4>
          <div class="notes-container row g-3" id="recentTasks"></div>
        </div>
        
        <!-- Sección de Tareas por Etiquetas -->
        <div class="notes-section">
          <h4 class="section-title mb-3"><i class="bi bi-tag"></i> Por Etiquetas</h4>
          <div class="label-filter d-flex flex-wrap gap-2 mb-3" id="labelFilter">
            <button class="btn btn-sm btn-outline-primary active" data-label-id="all">Todas</button>
            <!-- Los filtros de etiquetas se cargarán dinámicamente aquí -->
          </div>
          <div class="notes-container row g-3" id="labeledTasks"></div>
        </div>
      </div>
        
      <div class="empty-state text-center py-5 my-5">
        <i class="bi bi-check2-all display-4 text-muted mb-3"></i>
        <h3 class="text-muted">No hay tareas aún</h3>
        <p class="text-muted">Crea tu primera tarea haciendo clic en el botón "Nueva Tarea"</p>
        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#taskModal">
          <i class="bi bi-plus-lg"></i> Crear primera tarea
        </button>
      </div>
    </main>
  </div>

  <!-- Task Modal -->
  <div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-light">
          <h5 class="modal-title"><i class="bi bi-plus-lg"></i> <span id="taskModalTitle">Nueva Tarea</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="taskId">
          <div class="mb-3">
            <label for="taskTitle" class="form-label">Título</label>
            <input type="text" id="taskTitle" class="form-control" placeholder="Título de la tarea" required>
          </div>
          <div class="mb-3">
            <label for="taskContent" class="form-label">Contenido</label>
            <textarea id="taskContent" class="form-control" rows="4" placeholder="Descripción de la tarea..." required></textarea>
          </div>
          <div class="mb-3">
            <label for="taskLabel" class="form-label">Etiqueta</label>
            <select id="taskLabel" class="form-select">
              <option value="">Sin etiqueta</option>
              <!-- Las opciones de etiquetas se cargarán dinámicamente aquí -->
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger me-auto" id="btnDeleteTask" style="display: none;" onclick="confirmDeleteTask()">
            <i class="bi bi-trash"></i> Eliminar
          </button>
          <button type="button" class="btn btn-primary" onclick="saveTask()">Guardar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Label Modal -->
  <div class="modal fade" id="labelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-light">
          <h5 class="modal-title"><i class="bi bi-tag"></i> <span id="labelModalTitle">Nueva Etiqueta</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="labelId">
          <div class="mb-3">
            <label for="labelName" class="form-label">Nombre de la etiqueta</label>
            <input type="text" id="labelName" class="form-control" placeholder="Ej: Importante" required>
          </div>
          <div class="mb-3">
            <label for="labelColor" class="form-label">Color</label>
            <div class="color-palette d-flex align-items-center gap-3">
              <input type="color" id="labelColor" value="#FBBC05" class="form-control form-control-color">
              <div class="color-options d-flex gap-2">
                <button type="button" class="color-option" style="background-color: #4285F4;" data-color="#4285F4" title="Azul"></button>
                <button type="button" class="color-option" style="background-color: #EA4335;" data-color="#EA4335" title="Rojo"></button>
                <button type="button" class="color-option" style="background-color: #FBBC05;" data-color="#FBBC05" title="Amarillo"></button>
                <button type="button" class="color-option" style="background-color: #34A853;" data-color="#34A853" title="Verde"></button>
                <button type="button" class="color-option" style="background-color: #673AB7;" data-color="#673AB7" title="Morado"></button>
              </div>
            </div>
          </div>
          <div class="mb-3 form-check form-switch">
            <input type="checkbox" class="form-check-input" id="labelGlobal">
            <label class="form-check-label" for="labelGlobal">Etiqueta global (visible para todos los usuarios)</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger me-auto" id="btnDeleteLabel" style="display: none;" onclick="confirmDeleteLabel()">
            <i class="bi bi-trash"></i> Eliminar
          </button>
          <button type="button" class="btn btn-primary" onclick="saveLabel()">Guardar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/dashboard.js"></script>
</body>
</html>