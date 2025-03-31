document.addEventListener("DOMContentLoaded", function() {
  // Cargar datos iniciales
  loadTasks();
  loadLabels();
  
  // Evento de búsqueda
  document.querySelector('.search-box input').addEventListener('input', function(e) {
      searchTasks(e.target.value);
  });
  
  // Eventos de filtrado por etiquetas
  document.querySelectorAll('.label-filter button').forEach(btn => {
      btn.addEventListener('click', function() {
          filterByLabel(this.textContent.trim());
      });
  });
});

// Cargar tareas organizadas por secciones
function loadTasks() {
  fetch("../controllers/task.php")
      .then(response => response.json())
      .then(tasks => {
          updateTaskStats(tasks);
          renderTasks(tasks);
      })
      .catch(error => {
          console.error("Error loading tasks:", error);
          showError("Error al cargar tareas");
      });
}

// Cargar etiquetas del usuario
function loadLabels() {
  // Simulación - reemplazar con tu endpoint real
  const labels = [
      { name: "Trabajo", color: "#4285F4" },
      { name: "Personal", color: "#EA4335" },
      { name: "Importante", color: "#FBBC05" },
      { name: "Proyectos", color: "#34A853" }
  ];
  
  renderLabels(labels);
}

// Mostrar etiquetas en el sidebar
function renderLabels(labels) {
  const labelList = document.querySelector('.label-list');
  labelList.innerHTML = labels.map(label => `
      <li>
          <a href="#" data-label="${label.name}">
              <span class="label-color" style="background-color: ${label.color}"></span>
              ${label.name}
          </a>
      </li>
  `).join('');
  
  // Eventos para filtrar desde el sidebar
  labelList.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', function(e) {
          e.preventDefault();
          filterByLabel(this.dataset.label);
      });
  });
}

// Actualizar contadores de estadísticas
function updateTaskStats(tasks) {
  const total = tasks.length;
  const completed = tasks.filter(task => task.Status === 'completed').length;
  
  document.getElementById('totalTasks').textContent = total;
  document.getElementById('completedTasks').textContent = completed;
  document.getElementById('pendingTasks').textContent = total - completed;
}

// Renderizar tareas en las 3 secciones
function renderTasks(tasks) {
  if (tasks.length === 0) {
      showEmptyState();
      return;
  }
  
  hideEmptyState();
  
  // Sección Recientes (últimas 5)
  const recentTasks = tasks.slice(0, 5);
  renderSection('recentNotes', recentTasks, 'recentCount');
  
  // Sección Importantes (prioridad alta)
  const importantTasks = tasks.filter(task => task.Priority === 'high');
  renderSection('importantNotes', importantTasks, 'importantCount');
  
  // Sección Por Etiquetas (todas)
  renderSection('labeledNotes', tasks);
}

// Renderizar una sección específica
function renderSection(containerId, tasks, counterId = null) {
  const container = document.getElementById(containerId);
  
  if (counterId) {
      document.getElementById(counterId).textContent = tasks.length;
  }
  
  if (tasks.length === 0) {
      container.innerHTML = `<p class="text-muted">No hay tareas en esta sección</p>`;
      return;
  }
  
  container.innerHTML = tasks.map(task => `
      <div class="note-card ${task.Priority === 'high' ? 'important' : ''}">
          <div class="note-card-header">
              <h5 class="note-title">${task.Title}</h5>
              <span class="note-date">${formatDate(task.CreatedAt)}</span>
          </div>
          <div class="note-content">${task.Content}</div>
          <div class="note-footer">
              <span class="note-label" style="background-color: ${getLabelColor(task.Label)}">
                  ${task.Label || 'General'}
              </span>
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

// Mostrar estado vacío
function showEmptyState() {
  document.querySelectorAll('.empty-state').forEach(el => {
      el.style.display = 'block';
  });
  document.querySelectorAll('.notes-section').forEach(el => {
      el.style.display = 'none';
  });
}

// Ocultar estado vacío
function hideEmptyState() {
  document.querySelectorAll('.empty-state').forEach(el => {
      el.style.display = 'none';
  });
  document.querySelectorAll('.notes-section').forEach(el => {
      el.style.display = 'block';
  });
}

// Función para agregar nueva tarea (mejorada)
function addTask() {
  const title = document.getElementById('taskTitle').value.trim();
  const content = document.getElementById('taskContent').value.trim();
  const priority = document.getElementById('taskPriority').value;
  
  // Obtener etiquetas seleccionadas
  const selectedLabels = [];
  document.querySelectorAll('.label-selector input[type="checkbox"]:checked').forEach(checkbox => {
      selectedLabels.push(checkbox.value);
  });
  
  if (!title) {
      Swal.fire('Error', 'El título es obligatorio', 'error');
      return;
  }
  
  const taskData = {
      title: title,
      content: content,
      priority: priority,
      labels: selectedLabels
  };
  
  fetch("../controllers/task.php", {
      method: "POST",
      headers: {
          "Content-Type": "application/json",
      },
      body: JSON.stringify(taskData)
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          Swal.fire('Éxito', 'Tarea creada correctamente', 'success');
          document.getElementById('taskTitle').value = '';
          document.getElementById('taskContent').value = '';
          bootstrap.Modal.getInstance(document.getElementById('taskModal')).hide();
          loadTasks();
      } else {
          Swal.fire('Error', data.message || 'Error al crear tarea', 'error');
      }
  })
  .catch(error => {
      Swal.fire('Error', 'Error de conexión', 'error');
  });
}

// Función para editar tarea (mejorada)
function editTask(taskId) {
  // Obtener datos actuales de la tarea (simulado)
  const currentTask = {
      title: "Tarea de ejemplo",
      content: "Contenido de ejemplo",
      priority: "medium",
      label: "Trabajo"
  };
  
  Swal.fire({
      title: 'Editar Tarea',
      html: `
          <input id="editTitle" class="swal2-input" value="${currentTask.title}" placeholder="Título">
          <textarea id="editContent" class="swal2-textarea" placeholder="Descripción">${currentTask.content}</textarea>
          <select id="editPriority" class="swal2-select">
              <option value="low" ${currentTask.priority === 'low' ? 'selected' : ''}>Baja</option>
              <option value="medium" ${currentTask.priority === 'medium' ? 'selected' : ''}>Media</option>
              <option value="high" ${currentTask.priority === 'high' ? 'selected' : ''}>Alta</option>
          </select>
      `,
      focusConfirm: false,
      preConfirm: () => {
          return {
              title: document.getElementById('editTitle').value.trim(),
              content: document.getElementById('editContent').value.trim(),
              priority: document.getElementById('editPriority').value
          };
      }
  }).then((result) => {
      if (result.isConfirmed) {
          const { title, content, priority } = result.value;
          
          if (!title) {
              Swal.fire('Error', 'El título es obligatorio', 'error');
              return;
          }
          
          fetch(`../controllers/task.php?taskId=${taskId}`, {
              method: "PUT",
              headers: {
                  "Content-Type": "application/json",
              },
              body: JSON.stringify({ title, content, priority })
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  Swal.fire('Éxito', 'Tarea actualizada', 'success');
                  loadTasks();
              } else {
                  Swal.fire('Error', 'Error al actualizar', 'error');
              }
          });
      }
  });
}

// Función para eliminar tarea (mejorada)
function deleteTask(taskId) {
  Swal.fire({
      title: '¿Eliminar tarea?',
      text: "Esta acción no se puede deshacer",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
  }).then((result) => {
      if (result.isConfirmed) {
          fetch(`../controllers/task.php?taskId=${taskId}`, {
              method: "DELETE"
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  Swal.fire('Eliminada', 'La tarea ha sido eliminada', 'success');
                  loadTasks();
              } else {
                  Swal.fire('Error', 'No se pudo eliminar', 'error');
              }
          });
      }
  });
}

// Filtrar por etiqueta
function filterByLabel(label) {
  if (label === 'Todas') {
      document.querySelectorAll('.note-card').forEach(card => {
          card.style.display = 'block';
      });
      document.querySelectorAll('.label-filter button').forEach(btn => {
          btn.classList.remove('active');
      });
      document.querySelector('.label-filter button:first-child').classList.add('active');
      return;
  }
  
  document.querySelectorAll('.note-card').forEach(card => {
      const cardLabel = card.querySelector('.note-label').textContent;
      card.style.display = cardLabel === label ? 'block' : 'none';
  });
  
  // Actualizar botones activos
  document.querySelectorAll('.label-filter button').forEach(btn => {
      btn.classList.toggle('active', btn.textContent.trim() === label);
  });
}

// Buscar tareas
function searchTasks(query) {
  const searchTerm = query.toLowerCase();
  
  document.querySelectorAll('.note-card').forEach(card => {
      const title = card.querySelector('.note-title').textContent.toLowerCase();
      const content = card.querySelector('.note-content').textContent.toLowerCase();
      card.style.display = (title.includes(searchTerm) || content.includes(searchTerm)) ? 'block' : 'none';
  });
}

// Funciones auxiliares
function formatDate(dateString) {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
}

function getLabelColor(labelName) {
  const colors = {
      'Trabajo': '#4285F4',
      'Personal': '#EA4335',
      'Importante': '#FBBC05',
      'Proyectos': '#34A853'
  };
  return colors[labelName] || '#6c757d';
}

function showError(message) {
  Swal.fire('Error', message, 'error');
}