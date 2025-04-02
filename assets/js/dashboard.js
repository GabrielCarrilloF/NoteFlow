// Variables globales
let allTasks = [];
let allLabels = [];
let currentUserId = <?php echo $_SESSION["user_id"]; ?>;

// Funciones principales
function loadTasks() {
    showLoading();
    fetch("../controllers/task.php")
        .then(response => response.json())
        .then(tasks => {
            allTasks = tasks;
            updateTaskStats(tasks);
            renderAllTasks(tasks);
            renderRecentTasks(tasks);
            hideLoading();
        })
        .catch(error => {
            console.error("Error loading tasks:", error);
            showError("Error al cargar tareas");
            hideLoading();
        });
}

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
            showError("Error al cargar etiquetas");
        });
}

// Renderizado
function renderAllTasks(tasks) {
    const container = document.getElementById('allTasks');
    
    if (tasks.length === 0) {
        showEmptyState();
        return;
    }
    
    hideEmptyState();
    container.innerHTML = tasks.map(task => createTaskCard(task)).join('');
    document.getElementById('totalTasksBadge').textContent = tasks.length;
}

function renderRecentTasks(tasks) {
    const container = document.getElementById('recentTasks');
    const recentTasks = tasks.slice(0, 5);
    
    document.getElementById('recentCount').textContent = recentTasks.length;
    
    if (recentTasks.length === 0) {
        container.innerHTML = `<div class="col-12"><p class="text-muted">No hay tareas recientes</p></div>`;
        return;
    }
    
    container.innerHTML = recentTasks.map(task => createTaskCard(task, false)).join('');
}

function createTaskCard(task, showActions = true) {
    return `
        <div class="col-md-6 col-lg-4">
            <div class="note-card card mb-3" data-id="${task.NoteID}" data-label-id="${task.labels_id || ''}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="note-title card-title mb-0">${task.Title}</h5>
                        <span class="note-date text-muted small">${formatDate(task.CreatedAt)}</span>
                    </div>
                    <div class="note-content card-text mb-3">${task.Content}</div>
                    <div class="d-flex justify-content-between align-items-center">
                        ${task.labels_id ? `
                            <span class="note-label badge" style="background-color: ${getLabelColor(task.labels_id)}">
                                ${getLabelName(task.labels_id)}
                            </span>
                        ` : '<span class="note-label badge bg-secondary">Sin etiqueta</span>'}
                        ${showActions ? `
                            <div class="note-actions">
                                <button class="btn btn-sm btn-outline-primary" onclick="editTask(${task.NoteID})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger ms-1" onclick="deleteTask(${task.NoteID})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderSidebarLabels(labels) {
    const container = document.getElementById('sidebarLabels');
    container.innerHTML = labels.map(label => `
        <li class="position-relative">
            <a href="#" class="d-block py-2 px-3 text-decoration-none text-white" 
               data-id="${label.ID}" 
               onclick="filterByLabel(${label.ID})"
               style="border-left: 3px solid ${label.color}">
                ${label.name}
                ${label.is_global ? '<i class="bi bi-globe ms-1" title="Etiqueta global"></i>' : ''}
                ${!label.is_global ? `
                    <div class="label-actions">
                        <button class="btn btn-sm btn-link p-0 text-white" 
                                onclick="editLabel(event, ${label.ID}, '${label.name}', '${label.color}', ${label.is_global})">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                ` : ''}
            </a>
        </li>
    `).join('');
}

function renderLabelFilter(labels) {
    const container = document.getElementById('labelFilter');
    container.innerHTML = `
        <button class="btn btn-sm btn-outline-primary active" data-label-id="all">Todas</button>
        ${labels.map(label => `
            <button class="btn btn-sm btn-outline-primary" data-label-id="${label.ID}">
                <span class="label-color me-1" style="background-color: ${label.color}"></span>
                ${label.name}
            </button>
        `).join('')}
    `;
    
    container.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#labelFilter button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const labelId = this.dataset.labelId;
            filterByLabel(labelId === 'all' ? null : labelId);
        });
    });
}

function renderLabelOptions(labels) {
    const select = document.getElementById('taskLabel');
    select.innerHTML = `
        <option value="">Sin etiqueta</option>
        ${labels.map(label => `
            <option value="${label.ID}" data-color="${label.color}">
                ${label.name} ${label.is_global ? '(Global)' : ''}
            </option>
        `).join('')}
    `;
}

// Funciones de filtrado y búsqueda
function filterByLabel(labelId) {
    const container = document.getElementById('labeledTasks');
    
    if (!labelId) {
        container.innerHTML = allTasks.map(task => createTaskCard(task)).join('');
        return;
    }
    
    const filteredTasks = allTasks.filter(task => task.labels_id == labelId);
    
    if (filteredTasks.length === 0) {
        container.innerHTML = `<div class="col-12"><p class="text-muted">No hay tareas con esta etiqueta</p></div>`;
        return;
    }
    
    container.innerHTML = filteredTasks.map(task => createTaskCard(task)).join('');
}

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
        container.innerHTML = `<div class="col-12"><p class="text-muted">No se encontraron tareas</p></div>`;
        return;
    }
    
    container.innerHTML = filteredTasks.map(task => createTaskCard(task)).join('');
}

// Funciones para tareas
function saveTask() {
    const id = document.getElementById('taskId').value;
    const title = document.getElementById('taskTitle').value.trim();
    const content = document.getElementById('taskContent').value.trim();
    const labelId = document.getElementById('taskLabel').value || null;
    
    if (!title || !content) {
        showError('Todos los campos son obligatorios');
        return;
    }
    
    showLoading();
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
            showSuccess(id ? 'Tarea actualizada' : 'Tarea creada');
            loadTasks();
            bootstrap.Modal.getInstance(document.getElementById('taskModal')).hide();
        } else {
            showError(data.error || 'Error al guardar la tarea');
        }
    })
    .catch(error => {
        showError('Error de conexión');
    })
    .finally(() => hideLoading());
}

function editTask(taskId) {
    const task = allTasks.find(t => t.NoteID == taskId);
    if (task) {
        document.getElementById('taskId').value = task.NoteID;
        document.getElementById('taskTitle').value = task.Title;
        document.getElementById('taskContent').value = task.Content;
        document.getElementById('taskLabel').value = task.labels_id || '';
        document.getElementById('taskModalTitle').textContent = 'Editar Tarea';
        document.getElementById('btnDeleteTask').style.display = 'block';
        
        const modal = new bootstrap.Modal(document.getElementById('taskModal'));
        modal.show();
    }
}

function deleteTask(taskId) {
    confirmAction(
        '¿Eliminar tarea?', 
        '¿Estás seguro de que quieres eliminar esta tarea? Esta acción no se puede deshacer.',
        () => {
            showLoading();
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
                    showSuccess('Tarea eliminada');
                    loadTasks();
                } else {
                    showError('No se pudo eliminar la tarea');
                }
            })
            .catch(error => {
                showError('Error de conexión');
            })
            .finally(() => hideLoading());
        }
    );
}

// Funciones para etiquetas
function saveLabel() {
    const id = document.getElementById('labelId').value;
    const name = document.getElementById('labelName').value.trim();
    const color = document.getElementById('labelColor').value;
    const isGlobal = document.getElementById('labelGlobal').checked;
    
    if (!name) {
        showError('El nombre de la etiqueta es obligatorio');
        return;
    }
    
    showLoading();
    const url = "../controllers/label.php";
    const method = id ? "PUT" : "POST";
    const body = id 
        ? `id=${id}&name=${encodeURIComponent(name)}&color=${color}`
        : JSON.stringify({name, color, isGlobal});
    
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
            showSuccess(id ? 'Etiqueta actualizada' : 'Etiqueta creada');
            loadLabels();
            bootstrap.Modal.getInstance(document.getElementById('labelModal')).hide();
        } else {
            showError(data.error || 'Error al guardar la etiqueta');
        }
    })
    .catch(error => {
        showError('Error de conexión');
    })
    .finally(() => hideLoading());
}

function editLabel(event, id, name, color, isGlobal) {
    event.stopPropagation();
    document.getElementById('labelId').value = id;
    document.getElementById('labelName').value = name;
    document.getElementById('labelColor').value = color;
    document.getElementById('labelGlobal').checked = isGlobal;
    document.getElementById('labelModalTitle').textContent = 'Editar Etiqueta';
    document.getElementById('btnDeleteLabel').style.display = 'block';
    document.getElementById('labelGlobal').disabled = isGlobal;
    
    const modal = new bootstrap.Modal(document.getElementById('labelModal'));
    modal.show();
}

function deleteLabel(labelId) {
    confirmAction(
        '¿Eliminar etiqueta?', 
        '¿Estás seguro de que quieres eliminar esta etiqueta?<br><small>Las tareas asociadas no se eliminarán, pero perderán esta etiqueta.</small>',
        () => {
            showLoading();
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
                    showSuccess('Etiqueta eliminada');
                    loadLabels();
                    loadTasks();
                    bootstrap.Modal.getInstance(document.getElementById('labelModal')).hide();
                } else {
                    showError('No se pudo eliminar la etiqueta');
                }
            })
            .catch(error => {
                showError('Error de conexión');
            })
            .finally(() => hideLoading());
        }
    );
}

// Funciones auxiliares
function updateTaskStats(tasks) {
    document.getElementById('totalTasks').textContent = tasks.length;
    document.getElementById('pendingTasks').textContent = tasks.length; // Actualizar según lógica de estado
    document.getElementById('completedTasks').textContent = 0; // Actualizar según lógica de estado
}

function getLabelName(labelId) {
    const label = allLabels.find(l => l.ID == labelId);
    return label ? label.name : '';
}

function getLabelColor(labelId) {
    const label = allLabels.find(l => l.ID == labelId);
    return label ? label.color : '#6c757d';
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('es-ES', options);
}

function showEmptyState() {
    document.querySelector('.empty-state').style.display = 'flex';
    document.getElementById('taskList').style.display = 'none';
}

function hideEmptyState() {
    document.querySelector('.empty-state').style.display = 'none';
    document.getElementById('taskList').style.display = 'block';
}

function showLoading() {
    Swal.fire({
        title: 'Cargando...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function hideLoading() {
    Swal.close();
}

function showSuccess(message) {
    Swal.fire({
        title: 'Éxito',
        text: message,
        icon: 'success',
        confirmButtonText: 'Aceptar'
    });
}

function showError(message) {
    Swal.fire({
        title: 'Error',
        text: message,
        icon: 'error',
        confirmButtonText: 'Aceptar'
    });
}

function confirmAction(title, html, callback) {
    Swal.fire({
        title: title,
        html: html,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
}

// Inicialización
document.addEventListener("DOMContentLoaded", function() {
    loadTasks();
    loadLabels();
    
    // Evento de búsqueda
    document.getElementById('searchInput').addEventListener('input', function(e) {
        searchTasks(e.target.value);
    });
    
    // Eventos para los colores predefinidos
    document.querySelectorAll('.color-option').forEach(option => {
        option.addEventListener('click', function() {
            document.getElementById('labelColor').value = this.dataset.color;
        });
    });
    
    // Reset modales al cerrarse
    document.getElementById('labelModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('labelId').value = '';
        document.getElementById('labelName').value = '';
        document.getElementById('labelColor').value = '#FBBC05';
        document.getElementById('labelGlobal').checked = false;
        document.getElementById('labelGlobal').disabled = false;
        document.getElementById('btnDeleteLabel').style.display = 'none';
        document.getElementById('labelModalTitle').textContent = 'Nueva Etiqueta';
    });
    
    document.getElementById('taskModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('taskId').value = '';
        document.getElementById('taskTitle').value = '';
        document.getElementById('taskContent').value = '';
        document.getElementById('taskLabel').value = '';
        document.getElementById('btnDeleteTask').style.display = 'none';
        document.getElementById('taskModalTitle').textContent = 'Nueva Tarea';
    });
});