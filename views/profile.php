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

// Verificar que se haya obtenido el perfil
if (!$profile) {
    echo "Error: Profile not found. Please contact support.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>NoteFlow - Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <!-- Navbar común -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="#">NoteFlow</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">My Tasks</a></li>
          <li class="nav-item"><a class="nav-link active" href="#">Profile</a></li>
          <li class="nav-item"><a class="nav-link" href="../controllers/logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
  
  <div class="container mt-4">
    <?php if(isset($_SESSION["success"])): ?>
      <div class="alert alert-success"><?php echo $_SESSION["success"]; unset($_SESSION["success"]); ?></div>
    <?php endif; ?>
    <?php if(isset($_SESSION["error"])): ?>
      <div class="alert alert-danger"><?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?></div>
    <?php endif; ?>
    
    <h3>Your Profile</h3>
    <form action="../controllers/profile.php" method="POST">
      <div class="mb-3">
        <label for="fullName" class="form-label">Full Name</label>
        <input type="text" name="fullName" id="fullName" class="form-control" value="<?php echo htmlspecialchars($profile["FullName"]); ?>" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($profile["Email"]); ?>" required>
      </div>
      <div class="mb-3">
        <label for="username" class="form-label">Username (cannot be changed)</label>
        <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($profile["User"]); ?>" readonly>
      </div>
      <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
