document.addEventListener("DOMContentLoaded", function() {
    loadTasks();
  });
  
  function loadTasks() {
    fetch("../controllers/task.php")
      .then(response => response.json())
      .then(data => {
        let taskList = document.getElementById("taskList");
        taskList.innerHTML = "";
        if(data.length === 0) {
          taskList.innerHTML = "<p class='text-muted'>No tienes tareas aún.</p>";
          return;
        }
        data.forEach(task => {
          let taskItem = document.createElement("div");
          taskItem.classList.add("list-group-item");
          taskItem.innerHTML = `<h5>${task.Title}</h5>
                                <p>${task.Content}</p>
                                <small class="text-muted">Creado: ${task.CreatedAt}</small>
                                <div class="mt-2">
                                  <button class="btn btn-sm btn-secondary" onclick="editTask(${task.NoteID}, '${task.Title}', '${task.Content}')">Editar</button>
                                  <button class="btn btn-sm btn-danger" onclick="deleteTask(${task.NoteID})">Eliminar</button>
                                </div>`;
          taskList.appendChild(taskItem);
        });
      });
  }
  
  function addTask() {
    let title = document.getElementById("taskTitle").value.trim();
    let content = document.getElementById("taskContent").value.trim();
    
    if(title === "" || content === ""){
      alert("Todos los campos son obligatorios.");
      return;
    }
    
    fetch("../controllers/task.php", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: `title=${encodeURIComponent(title)}&content=${encodeURIComponent(content)}`
    })
    .then(response => response.json())
    .then(data => {
      if(data.success){
        document.getElementById("taskTitle").value = "";
        document.getElementById("taskContent").value = "";
        let taskModal = bootstrap.Modal.getInstance(document.getElementById("taskModal"));
        taskModal.hide();
        loadTasks();
      } else {
        alert("Error al guardar la tarea.");
      }
    });
  }
  
  function editTask(taskId, currentTitle, currentContent) {
    let newTitle = prompt("Editar título:", currentTitle);
    if(newTitle === null) return;
    let newContent = prompt("Editar contenido:", currentContent);
    if(newContent === null) return;
    
    fetch("../controllers/task.php", {
      method: "PUT",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: `taskId=${taskId}&title=${encodeURIComponent(newTitle)}&content=${encodeURIComponent(newContent)}`
    })
    .then(response => response.json())
    .then(data => {
      if(data.success){
        loadTasks();
      } else {
        alert("Error al actualizar la tarea.");
      }
    });
  }
  
  function deleteTask(taskId) {
    if(!confirm("¿Estás seguro de eliminar esta tarea?")) return;
    
    fetch("../controllers/task.php", {
      method: "DELETE",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: `taskId=${taskId}`
    })
    .then(response => response.json())
    .then(data => {
      if(data.success){
        loadTasks();
      } else {
        alert("Error al eliminar la tarea.");
      }
    });
  }
  