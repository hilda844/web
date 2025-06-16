<?php
session_start();
include("conexion.php");

// Variables de sesión
$nombre = $_SESSION['nombre'] ?? 'Visitante';
$rol = $_SESSION['rol'] ?? 'Visitante';
$tipo = $_SESSION['tipo'] ?? 0;

// Datos del cliente si es tipo 3
$datos_cliente = null;
$nombre_completo = $nombre;

if ($tipo == 3) {
    $sql = "SELECT u.nombre, u.apellido_paterno, u.apellido_materno, u.telefono, u.fecha_creacion
            FROM usuarios u
            WHERE u.nombre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        $datos_cliente = $resultado->fetch_assoc();
        $nombre_completo = $datos_cliente['nombre'] . ' ' . $datos_cliente['apellido_paterno'] . ' ' . $datos_cliente['apellido_materno'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Abarrotes Angelito</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

  <div class="header">
    <div class="logo-container">
      <img src="img/logo.png" alt="Logo">
      <h2>ABARROTES <span>ANGELITO</span></h2>
    </div>

    <div class="search-container">
      <input type="text" placeholder="Búsqueda">
      <img src="img/lupa.png" alt="Buscar">
    </div>

    <div class="nav-buttons">
      <a href="logout.php">Salir</a>
      <a href="carrito.php">Carrito de compras</a>
      <a href="perfil.php">Perfil</a>
    </div>
  </div>

  <!-- Mensaje de bienvenida -->
  <div style="text-align:center; padding: 15px;">
    <h3>Bienvenido, <?php echo htmlspecialchars($nombre_completo); ?></h3>
  </div>

  <hr>

  <div class="categories">
    <button onclick="location.href='abarrotes.php'">
      <img src="img/abarrotes.png" alt="Abarrotes y Despensa">
      <p>Abarrotes y Despensa</p>
    </button>
    <button onclick="location.href='lacteos.php'">
      <img src="img/lacteos.png" alt="Lácteos">
      <p>Lácteos</p>
    </button>
    <button onclick="location.href='limpieza.php'">
      <img src="img/limpieza.png" alt="Productos de Limpieza">
      <p>Productos de Limpieza y Cuidado Personal</p>
    </button>
  </div>

</body>
</html>
