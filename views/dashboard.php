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
          
          <!-- Sección de Etiquetas -->
          <li class="sidebar-section">
            <div class="section-header">
              <span>Etiquetas</span>
              <button class="btn btn-sm btn-link p-0" data-bs-toggle="modal" data-bs-target="#labelModal">
                <i class="bi bi-plus-lg"></i>
              </button>
            </div>
            <ul class="label-list" id="sidebarLabels">
              <!-- Las etiquetas se cargarán dinámicamente aquí -->
            </ul>
          </li>
          
          <li><a href="../controllers/logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-content">
      <header class="content-header">
        <h1><i class="bi bi-check-circle"></i> Mis Tareas</h1>
        <div class="header-actions">
          <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="Buscar tareas...">
          </div>
          <div class="filter-dropdown">
            <div class="sidebar-footer">
              <button class="btn btn-primary new-task-btn" data-bs-toggle="modal" data-bs-target="#taskModal">
                <i class="bi bi-plus-lg"></i> Nueva Tarea
              </button>
            </div>
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
        <!-- Todas las tareas se mostrarán aquí -->
        <div class="notes-container" id="allTasks"></div>
        
        <!-- Sección de Tareas Recientes -->
        <div class="notes-section">
          <h4 class="section-title">
            <i class="bi bi-clock"></i> Recientes
            <span class="badge bg-primary rounded-pill" id="recentCount">0</span>
          </h4>
          <div class="notes-container" id="recentTasks"></div>
        </div>
        
        <!-- Sección de Tareas por Etiquetas -->
        <div class="notes-section">
          <h4 class="section-title">
            <i class="bi bi-tag"></i> Por Etiquetas
          </h4>
          <div class="label-filter" id="labelFilter">
            <button class="btn btn-sm btn-outline-primary active" data-label-id="all">Todas</button>
            <!-- Los filtros de etiquetas se cargarán dinámicamente aquí -->
          </div>
          <div class="notes-container" id="labeledTasks"></div>
        </div>
      </div>
        
      <div class="empty-state">
        <i class="bi bi-check2-all"></i>
        <h3>No hay tareas aún</h3>
        <p>Crea tu primera tarea haciendo clic en el botón "Nueva Tarea"</p>
      </div>
    </main>
  </div>

  <!-- Task Modal -->
  <div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-plus-lg"></i> <span id="taskModalTitle">Nueva Tarea</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="taskId">
          <div class="mb-3">
            <label for="taskTitle" class="form-label">Título</label>
            <input type="text" id="taskTitle" class="form-control" placeholder="Título de la tarea">
          </div>
          <div class="mb-3">
            <label for="taskContent" class="form-label">Contenido</label>
            <textarea id="taskContent" class="form-control" rows="4" placeholder="Descripción de la tarea..."></textarea>
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
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-tag"></i> <span id="labelModalTitle">Nueva Etiqueta</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="labelId">
          <div class="mb-3">
            <label for="labelName" class="form-label">Nombre de la etiqueta</label>
            <input type="text" id="labelName" class="form-control" placeholder="Ej: Importante">
          </div>
          <div class="mb-3">
            <label for="labelColor" class="form-label">Color</label>
            <div class="color-palette">
              <input type="color" id="labelColor" value="#FBBC05" class="form-control form-control-color">
              <div class="color-options">
                <button type="button" class="color-option" style="background-color: #4285F4;" data-color="#4285F4"></button>
                <button type="button" class="color-option" style="background-color: #EA4335;" data-color="#EA4335"></button>
                <button type="button" class="color-option" style="background-color: #FBBC05;" data-color="#FBBC05"></button>
                <button type="button" class="color-option" style="background-color: #34A853;" data-color="#34A853"></button>
                <button type="button" class="color-option" style="background-color: #673AB7;" data-color="#673AB7"></button>
                <button type="button" class="color-option" style="background-color: #FF5722;" data-color="#FF5722"></button>
              </div>
            </div>
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
  <script>
  // Variables globales
  let allTasks = [];
  let allLabels = [];

  // Cargar tareas y etiquetas al iniciar
  document.addEventListener("DOMContentLoaded", function() {
    loadTasks();
    loadLabels();
    
    // Evento de búsqueda
    document.getElementById('searchInput').addEventListener('input', function(e) {
      searchTasks(e.target.value);
    });
  });

  // Cargar tareas
  function loadTasks() {
    fetch("../controllers/task.php")
      .then(response => response.json())
      .then(tasks => {
        allTasks = tasks;
        updateTaskStats(tasks);
        renderAllTasks(tasks);
        renderRecentTasks(tasks);
      })
      .catch(error => {
        console.error("Error loading tasks:", error);
        showError("Error al cargar tareas");
      });
  }

  // Cargar etiquetas
  function loadLabels() {
    fetch("../controllers/label.php")
      .then(response => response.json())
      .then(labels => {
        allLabels = labels;
        renderSidebarLabels(labels);
        renderLabelFilter(labels);
        renderLabelOptions(labels);
      })
      .catch(error => {
        console.error("Error loading labels:", error);
      });
  }

  // Actualizar estadísticas
  function updateTaskStats(tasks) {
    document.getElementById('totalTasks').textContent = tasks.length;
    document.getElementById('pendingTasks').textContent = tasks.length; // Cambiar según tu lógica
    document.getElementById('completedTasks').textContent = 0; // Cambiar según tu lógica
  }

  // Renderizar todas las tareas
  function renderAllTasks(tasks) {
    const container = document.getElementById('allTasks');
    
    if (tasks.length === 0) {
      showEmptyState();
      return;
    }
    
    hideEmptyState();
    
    container.innerHTML = tasks.map(task => `
      <div class="note-card" data-id="${task.NoteID}" data-label-id="${task.labels_id || ''}">
        <div class="note-card-header">
          <h5 class="note-title">${task.Title}</h5>
          <span class="note-date">${formatDate(task.CreatedAt)}</span>
        </div>
        <div class="note-content">${task.Content}</div>
        <div class="note-footer">
          ${task.labels_id ? `
            <span class="note-label" style="background-color: ${getLabelColor(task.labels_id)}">
              ${getLabelName(task.labels_id)}
            </span>
          ` : '<span class="note-label no-label">Sin etiqueta</span>'}
          <div>
            <button class="btn btn-sm btn-outline-secondary" onclick="editTask(${task.NoteID})">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="deleteTask(${task.NoteID})">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      </div>
    `).join('');
  }

  // Renderizar tareas recientes
  function renderRecentTasks(tasks) {
    const container = document.getElementById('recentTasks');
    const recentTasks = tasks.slice(0, 5); // Últimas 5 tareas
    
    document.getElementById('recentCount').textContent = recentTasks.length;
    
    if (recentTasks.length === 0) {
      container.innerHTML = `<p class="text-muted">No hay tareas recientes</p>`;
      return;
    }
    
    container.innerHTML = recentTasks.map(task => `
      <div class="note-card" data-id="${task.NoteID}">
        <div class="note-card-header">
          <h5 class="note-title">${task.Title}</h5>
          <span class="note-date">${formatDate(task.CreatedAt)}</span>
        </div>
        <div class="note-content">${task.Content}</div>
      </div>
    `).join('');
  }

  // Renderizar etiquetas en el sidebar
  function renderSidebarLabels(labels) {
    const container = document.getElementById('sidebarLabels');
    container.innerHTML = labels.map(label => `
      <li>
        <a href="#" data-id="${label.ID}" onclick="filterByLabel(${label.ID})">
          <span class="label-color" style="background-color: ${label.color}"></span>
          ${label.name}
          <div class="label-actions">
            <button class="btn btn-sm btn-link p-0" onclick="editLabel(event, ${label.ID}, '${label.name}', '${label.color}')">
              <i class="bi bi-pencil"></i>
            </button>
          </div>
        </a>
      </li>
    `).join('');
  }

  // Renderizar filtros de etiquetas
  function renderLabelFilter(labels) {
    const container = document.getElementById('labelFilter');
    container.innerHTML = `
      <button class="btn btn-sm btn-outline-primary active" data-label-id="all">Todas</button>
      ${labels.map(label => `
        <button class="btn btn-sm btn-outline-primary" data-label-id="${label.ID}">
          <span class="label-color" style="background-color: ${label.color}"></span>
          ${label.name}
        </button>
      `).join('')}
    `;
    
    // Event listeners para los filtros
    container.querySelectorAll('button').forEach(btn => {
      btn.addEventListener('click', function() {
        const labelId = this.dataset.labelId;
        filterByLabel(labelId === 'all' ? null : labelId);
      });
    });
  }

  // Renderizar opciones de etiquetas en el modal
  function renderLabelOptions(labels) {
    const select = document.getElementById('taskLabel');
    select.innerHTML = `
      <option value="">Sin etiqueta</option>
      ${labels.map(label => `
        <option value="${label.ID}" data-color="${label.color}">${label.name}</option>
      `).join('')}
    `;
  }

  // Funciones auxiliares
  function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('es-ES', options);
  }

  function getLabelName(labelId) {
    const label = allLabels.find(l => l.ID == labelId);
    return label ? label.name : '';
  }

  function getLabelColor(labelId) {
    const label = allLabels.find(l => l.ID == labelId);
    return label ? label.color : '#6c757d';
  }

  function showEmptyState() {
    document.querySelector('.empty-state').style.display = 'flex';
    document.getElementById('taskList').style.display = 'none';
  }

  function hideEmptyState() {
    document.querySelector('.empty-state').style.display = 'none';
    document.getElementById('taskList').style.display = 'block';
  }

  function showError(message) {
    Swal.fire('Error', message, 'error');
  }

  // Funciones para tareas (similar a las anteriores)
  function saveTask() {
    const id = document.getElementById('taskId').value;
    const title = document.getElementById('taskTitle').value.trim();
    const content = document.getElementById('taskContent').value.trim();
    const labelId = document.getElementById('taskLabel').value || null;
    
    if (!title) {
      Swal.fire('Error', 'El título es obligatorio', 'error');
      return;
    }
    
    const url = "../controllers/task.php";
    const method = id ? "PUT" : "POST";
    const body = id 
      ? `noteId=${id}&title=${encodeURIComponent(title)}&content=${encodeURIComponent(content)}&labelId=${labelId || ''}`
      : JSON.stringify({title, content, labelId});
    
    fetch(url, {
      method: method,
      headers: {
        "Content-Type": id ? "application/x-www-form-urlencoded" : "application/json",
      },
      body: body
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        Swal.fire('Éxito', id ? 'Tarea actualizada' : 'Tarea creada', 'success');
        loadTasks();
        bootstrap.Modal.getInstance(document.getElementById('taskModal')).hide();
      } else {
        Swal.fire('Error', data.error || 'Error al guardar', 'error');
      }
    })
    .catch(error => {
      Swal.fire('Error', 'Error de conexión', 'error');
    });
  }

  function editTask(taskId) {
    const task = allTasks.find(t => t.NoteID == taskId);
    if (task) {
      openTaskModal(true, {
        id: task.NoteID,
        title: task.Title,
        content: task.Content,
        labelId: task.labels_id || null
      });
    }
  }

  function openTaskModal(editMode = false, taskData = null) {
    const modal = new bootstrap.Modal(document.getElementById('taskModal'));
    const modalTitle = document.getElementById('taskModalTitle');
    const btnDelete = document.getElementById('btnDeleteTask');
    
    if (editMode && taskData) {
      modalTitle.textContent = 'Editar Tarea';
      document.getElementById('taskId').value = taskData.id;
      document.getElementById('taskTitle').value = taskData.title;
      document.getElementById('taskContent').value = taskData.content;
      document.getElementById('taskLabel').value = taskData.labelId || '';
      btnDelete.style.display = 'block';
    } else {
      modalTitle.textContent = 'Nueva Tarea';
      document.getElementById('taskId').value = '';
      document.getElementById('taskTitle').value = '';
      document.getElementById('taskContent').value = '';
      document.getElementById('taskLabel').value = '';
      btnDelete.style.display = 'none';
    }
    
    modal.show();
  }

  function deleteTask(taskId) {
    Swal.fire({
      title: '¿Eliminar tarea?',
      html: `¿Estás seguro de que quieres eliminar esta tarea?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch("../controllers/task.php", {
          method: "DELETE",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `noteId=${taskId}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire('Eliminada', 'La tarea ha sido eliminada', 'success');
            loadTasks();
          } else {
            Swal.fire('Error', 'No se pudo eliminar la tarea', 'error');
          }
        });
      }
    });
  }

  function confirmDeleteTask() {
    const id = document.getElementById('taskId').value;
    const title = document.getElementById('taskTitle').value;
    
    Swal.fire({
      title: '¿Eliminar tarea?',
      html: `¿Estás seguro de que quieres eliminar la tarea <strong>"${title}"</strong>?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        deleteTask(id);
      }
    });
  }

  // Funciones para etiquetas (similar a las anteriores)
  function saveLabel() {
    const id = document.getElementById('labelId').value;
    const name = document.getElementById('labelName').value.trim();
    const color = document.getElementById('labelColor').value;
    
    if (!name) {
      Swal.fire('Error', 'El nombre es obligatorio', 'error');
      return;
    }
    
    const url = "../controllers/label.php";
    const method = id ? "PUT" : "POST";
    const body = id 
      ? `id=${id}&name=${encodeURIComponent(name)}&color=${color}`
      : JSON.stringify({name, color});
    
    fetch(url, {
      method: method,
      headers: {
        "Content-Type": id ? "application/x-www-form-urlencoded" : "application/json",
      },
      body: body
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        Swal.fire('Éxito', id ? 'Etiqueta actualizada' : 'Etiqueta creada', 'success');
        loadLabels();
        bootstrap.Modal.getInstance(document.getElementById('labelModal')).hide();
      } else {
        Swal.fire('Error', data.error || 'Error al guardar', 'error');
      }
    })
    .catch(error => {
      Swal.fire('Error', 'Error de conexión', 'error');
    });
  }

  function editLabel(event, id, name, color) {
    event.stopPropagation();
    openLabelModal(true, {id, name, color});
  }

  function openLabelModal(editMode = false, labelData = null) {
    const modal = new bootstrap.Modal(document.getElementById('labelModal'));
    const modalTitle = document.getElementById('labelModalTitle');
    const btnDelete = document.getElementById('btnDeleteLabel');
    
    if (editMode && labelData) {
      modalTitle.textContent = 'Editar Etiqueta';
      document.getElementById('labelId').value = labelData.id;
      document.getElementById('labelName').value = labelData.name;
      document.getElementById('labelColor').value = labelData.color;
      btnDelete.style.display = 'block';
    } else {
      modalTitle.textContent = 'Nueva Etiqueta';
      document.getElementById('labelId').value = '';
      document.getElementById('labelName').value = '';
      document.getElementById('labelColor').value = '#FBBC05';
      btnDelete.style.display = 'none';
    }
    
    modal.show();
  }

  function confirmDeleteLabel() {
    const id = document.getElementById('labelId').value;
    const name = document.getElementById('labelName').value;
    
    Swal.fire({
      title: '¿Eliminar etiqueta?',
      html: `¿Estás seguro de que quieres eliminar la etiqueta <strong>"${name}"</strong>?<br><small>Todas las tareas asociadas perderán esta etiqueta.</small>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        deleteLabel(id);
      }
    });
  }

  function deleteLabel(labelId) {
    fetch("../controllers/label.php", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `id=${labelId}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        Swal.fire('Eliminada', 'La etiqueta ha sido eliminada', 'success');
        loadLabels();
        loadTasks(); // Recargar tareas para actualizar las que tenían esta etiqueta
        bootstrap.Modal.getInstance(document.getElementById('labelModal')).hide();
      } else {
        Swal.fire('Error', 'No se pudo eliminar la etiqueta', 'error');
      }
    });
  }

  // Filtrar por etiqueta
  function filterByLabel(labelId) {
    const container = document.getElementById('labeledTasks');
    
    if (!labelId) {
      // Mostrar todas las tareas
      container.innerHTML = allTasks.map(task => `
        <div class="note-card" data-id="${task.NoteID}">
          <div class="note-card-header">
            <h5 class="note-title">${task.Title}</h5>
            <span class="note-date">${formatDate(task.CreatedAt)}</span>
          </div>
          <div class="note-content">${task.Content}</div>
        </div>
      `).join('');
      return;
    }
    
    // Mostrar solo tareas con la etiqueta seleccionada
    const filteredTasks = allTasks.filter(task => task.labels_id == labelId);
    
    if (filteredTasks.length === 0) {
      container.innerHTML = `<p class="text-muted">No hay tareas con esta etiqueta</p>`;
      return;
    }
    
    container.innerHTML = filteredTasks.map(task => `
      <div class="note-card" data-id="${task.NoteID}">
        <div class="note-card-header">
          <h5 class="note-title">${task.Title}</h5>
          <span class="note-date">${formatDate(task.CreatedAt)}</span>
        </div>
        <div class="note-content">${task.Content}</div>
      </div>
    `).join('');
  }

  // Buscar tareas
  function searchTasks(query) {
    const searchTerm = query.toLowerCase();
    const container = document.getElementById('allTasks');
    
    if (!searchTerm) {
      renderAllTasks(allTasks);
      return;
    }
    
    const filteredTasks = allTasks.filter(task => 
      task.Title.toLowerCase().includes(searchTerm) || 
      task.Content.toLowerCase().includes(searchTerm)
    );
    
    if (filteredTasks.length === 0) {
      container.innerHTML = `<p class="text-muted">No se encontraron tareas</p>`;
      return;
    }
    
    container.innerHTML = filteredTasks.map(task => `
      <div class="note-card" data-id="${task.NoteID}">
        <div class="note-card-header">
          <h5 class="note-title">${task.Title}</h5>
          <span class="note-date">${formatDate(task.CreatedAt)}</span>
        </div>
        <div class="note-content">${task.Content}</div>
      </div>
    `).join('');
  }

  // Event listeners para los colores predefinidos
  document.querySelectorAll('.color-option').forEach(option => {
    option.addEventListener('click', function() {
      document.getElementById('labelColor').value = this.dataset.color;
    });
  });
  </script>
</body>
</html>