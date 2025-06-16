<?php
session_start();
include("conexion.php");

// Obtener productos de la categoría correspondiente
$sql = "SELECT * FROM productos WHERE categoria = 'Productos de Limpieza y Cuidado Personal'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$resultado = $stmt->get_result();

// Detectar tipo de usuario
$tipo = $_SESSION['tipo'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Productos de Limpieza y Cuidado Personal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
  <h2>🧴 Productos de Limpieza y Cuidado Personal</h2>
  <div class="row">
    <?php while ($producto = $resultado->fetch_assoc()): ?>
      <div class="col-md-4 mb-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($producto['nombre']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($producto['descripcion']) ?></p>
            <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 2) ?></p>
            <p><strong>Stock:</strong> <?= $producto['stock'] ?></p>

            <!-- Mostrar formulario de carrito o alerta según tipo de usuario -->
            <?php if ($tipo == 3): ?>
              <!-- Cliente logueado: formulario activo -->
              <form action="agregar_carrito.php" method="POST">
                <input type="hidden" name="idproducto" value="<?= $producto['idproductos'] ?>">
                <input type="number" name="cantidad" value="1" min="1" max="<?= $producto['stock'] ?>" class="form-control mb-2" required>
                <button type="submit" class="btn btn-primary">Agregar al carrito</button>
              </form>
            <?php else: ?>
              <!-- Visitante: botón bloqueado con alerta -->
              <input type="number" value="1" class="form-control mb-2" disabled>
              <button class="btn btn-secondary" onclick="alert('⚠️ Debes iniciar sesión para agregar productos al carrito.')" disabled>
                Agregar al carrito
              </button>
            <?php endif; ?>

          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
  <a href="tienda.php" class="btn btn-secondary mt-3">← Volver a la tienda</a>
</div>
</body>
</html>
