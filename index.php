<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Bienvenido a Abarrotes Angelito</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #e0f2f1;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
   
    .container-box {
      text-align: center;
      background: #FFFFFF;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    .btn {
      width: 200px;
      margin: 10px;
    }
    .logo {
      width: 120px;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <div class="container-box">
    <img src="img/logo.png" alt="Logo" class="logo"> <!-- Cambia ruta si es necesario -->
    <h2>Bienvenido a Abarrotes Angelito</h2>
    <p>Elige cómo deseas continuar:</p>
    <a href="registro.php" class="btn btn-primary">Crear cuenta</a><br>
    <a href="login.php" class="btn btn-success">Iniciar sesión</a><br>
    <a href="visitante.php" class="btn btn-secondary">Entrar como visitante</a>
  </div>
</body>
</html>


