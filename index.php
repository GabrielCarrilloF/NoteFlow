<?php
session_start();
if(isset($_SESSION['user_id'])){
    header("Location: views/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>NoteFlow - Login</title>
  <link rel="stylesheet" href="assets/css/index.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
  <div class="login-container p-4 rounded shadow">
        <div class="text-center">
            <img src="assets/img/logo app.jpg" alt="Logo" class="logo">
            <h2 class="mt-3">NoteFlow</h2>
        </div>
    <?php
    if(isset($_SESSION['error'])){
      echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
      unset($_SESSION['error']);
    }
    ?>
    <form action="controllers/login.php" method="POST">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <div class="mt-3 text-center">
      <a href="register.php">Register a new account</a>
    </div>
  </div>
</body>
</html>
