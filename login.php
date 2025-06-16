
<?php
session_start(); // Inicia la sesión al principio de la página
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f1f1f1;
    }
    .login-container {
      width: 340px;
      margin: auto;
      margin-top: 100px;
      padding: 30px;
      background-color: #ffffff;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>

<div class="login-container">
  <h4 class="text-center mb-4">🔐 Iniciar Sesión</h4>

  <form action="validar_login.php" method="POST">
    <div class="mb-3">
      <label class="form-label">Nombre de usuario</label>
      <input type="text" name="usuario" class="form-control" required placeholder="Ej. admin">
    </div>
    <div class="mb-3">
      <label class="form-label">Contraseña</label>
      <input type="password" name="contraseña" class="form-control" required placeholder="********">
    </div>
    <button type="submit" class="btn btn-success w-100">Entrar</button>
  </form>

  <div class="text-center mt-3">
    <small>¿No tienes cuenta? <a href="registro.php">Regístrate</a></small>
  </div>
</div>

</body>
</html>
