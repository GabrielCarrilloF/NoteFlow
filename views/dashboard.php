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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>NoteFlow - Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="#">NoteFlow</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="#">My Tasks</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Profile</a></li>
          <li class="nav-item"><a class="nav-link" href="../controllers/logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body text-center">
            <h5 class="card-title">Bienvenido, <?php echo htmlspecialchars($firstName); ?></h5>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="d-flex justify-content-between mb-3">
          <h3>Tus Tareas</h3>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taskModal">+ Nueva Tarea</button>
        </div>
        <div id="taskList" class="list-group"></div>
      </div>
    </div>
  </div>
  
  <!-- Modal para Nueva Tarea -->
  <div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nueva Tarea</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" id="taskTitle" class="form-control mb-2" placeholder="Título">
          <textarea id="taskContent" class="form-control" rows="4" placeholder="Descripción de la tarea"></textarea>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" onclick="addTask()">Guardar</button>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/dashboard.js"></script>
</body>
</html>
