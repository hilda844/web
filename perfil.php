<?php
session_start();
include("conexion.php");

// Verificar si hay sesión
if (!isset($_SESSION['nombre']) || $_SESSION['tipo'] != 3) {
    header("Location: index.php");
    exit();
}

$nombre = $_SESSION['nombre'];

// Obtener datos del cliente desde la tabla `clientes`
$sql = "SELECT * FROM clientes WHERE nombre = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nombre);
$stmt->execute();
$resultado = $stmt->get_result();

$cliente = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Perfil</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h2>👤 Mi Perfil</h2>

  <?php if ($cliente): ?>
    <table class="table table-bordered mt-3">
      <tr><th>Nombre:</th><td><?= $cliente['nombre'] ?></td></tr>
      <tr><th>Apellido Paterno:</th><td><?= $cliente['apellido_paterno'] ?></td></tr>
      <tr><th>Apellido Materno:</th><td><?= $cliente['apellido_materno'] ?></td></tr>
      <tr><th>Ciudad:</th><td><?= $cliente['ciudad'] ?></td></tr>
      <tr><th>Número y Calle:</th><td><?= $cliente['num_calle'] ?></td></tr>
      <tr><th>Comuna:</th><td><?= $cliente['comuna'] ?></td></tr>
      <tr><th>Teléfono:</th><td><?= $cliente['telefono'] ?></td></tr>
      <tr><th>Registrado el:</th><td><?= $cliente['fecha_registro'] ?></td></tr>
    </table>
    <a href="tienda.php" class="btn btn-primary">Volver a la tienda</a>
    <a href="mis_pedidos.php" class="btn btn-primary"> Mis Pedidos</a>
  <?php else: ?>
    <div class="alert alert-danger">No se encontraron los datos del cliente.</div>
  <?php endif; ?>
</div>
</body>
</html>
