// Funciones para tareas - Versión actualizada
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
    const body = JSON.stringify({
        noteId: id || undefined,
        title: title,
        content: content,
        labelId: labelId
    });
    
    fetch(url, {
        method: method,
        headers: {
            "Content-Type": "application/json",
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
        console.error("Error:", error);
        showError('Error de conexión');
    })
    .finally(() => hideLoading());
}

// Función para cargar etiquetas actualizada
function loadLabels() {
    fetch("../controllers/label.php")
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
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

// Función para crear tarjetas de tareas actualizada
function createTaskCard(task, showActions = true) {
    const labelInfo = task.labels_id ? {
        name: task.label_name || getLabelName(task.labels_id),
        color: task.label_color || getLabelColor(task.labels_id)
    } : null;
    
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
                        ${labelInfo ? `
                            <span class="note-label badge" style="background-color: ${labelInfo.color}">
                                ${labelInfo.name}
                            </span>
                        ` : '<span class="note-label badge bg-secondary">Sin etiqueta</span>'}
                        ${showActions ? `
                            <div class="note-actions">
                                <button class="btn btn-sm btn-outline-primary" onclick="editTask(${task.NoteID}, event)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger ms-1" onclick="deleteTask(${task.NoteID}, event)">
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

// Función para editar tarea actualizada
function editTask(taskId, event) {
    if (event) event.stopPropagation();
    
    const task = allTasks.find(t => t.NoteID == taskId);
    if (task) {
        document.getElementById('taskId').value = task.NoteID;
        document.getElementById('taskTitle').value = task.Title;
        document.getElementById('taskContent').value = task.Content;
        if (task.labels_id) {
            document.getElementById('taskLabel').value = task.labels_id;
        } else {
            document.getElementById('taskLabel').value = '';
        }
        document.getElementById('taskModalTitle').textContent = 'Editar Tarea';
        document.getElementById('btnDeleteTask').style.display = 'block';
        
        const modal = new bootstrap.Modal(document.getElementById('taskModal'));
        modal.show();
    }
}

// Funciones para etiquetas actualizadas
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
    const body = JSON.stringify({
        id: id || undefined,
        name: name,
        color: color,
        isGlobal: isGlobal
    });
    
    fetch(url, {
        method: method,
        headers: {
            "Content-Type": "application/json",
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
        console.error("Error:", error);
        showError('Error de conexión');
    })
    .finally(() => hideLoading());
}