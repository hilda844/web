<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Cuenta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Agregar estilo para los campos obligatorios */
    .form-label.required:after {
        content: " *";
        color: red;
    }
  </style>
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="card mx-auto" style="max-width: 500px;">
    <div class="card-header bg-primary text-white text-center">
      <h4>Crear Cuenta</h4>
    </div>
    <div class="card-body">
      <form action="guardar_usuario.php" method="POST">

        <!-- Nombre -->
        <label for="nombre" class="form-label required">Nombre:</label>
        <input type="text" name="nombre" id="nombre" class="form-control mb-2" required>

        <!-- Apellido paterno -->
        <label for="apellido_paterno" class="form-label">Apellido paterno:</label>
        <input type="text" name="apellido_paterno" id="apellido_paterno" class="form-control mb-2" required>

        <!-- Apellido materno -->
        <label for="apellido_materno" class="form-label">Apellido materno:</label>
        <input type="text" name="apellido_materno" id="apellido_materno" class="form-control mb-2" required>

        <!-- Ciudad -->
        <label for="ciudad" class="form-label">Ciudad:</label>
        <input type="text" name="ciudad" id="ciudad" class="form-control mb-2" required>

        <!-- Teléfono -->
        <label for="telefono" class="form-label">Teléfono:</label>
        <input type="text" name="telefono" id="telefono" class="form-control mb-2" required>

        <!-- Contraseña -->
        <label for="contraseña" class="form-label required">Contraseña:</label>
        <input type="password" name="contraseña" id="contraseña" class="form-control mb-3" required>

        <!-- Rol: Administrador o Cliente -->
        <label for="rol" class="form-label">Rol:</label>
        <select name="rol" id="rol" class="form-select mb-3" required>
          <option value="cliente">Cliente</option>
          <option value="administrador">Administrador</option>
        </select>

        <!-- Botón para enviar el formulario -->
        <div class="d-grid">
          <button type="submit" class="btn btn-success">Crear cuenta</button>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>
