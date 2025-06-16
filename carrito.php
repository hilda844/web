<?php
session_start();
include("conexion.php");

// Verifica si el cliente está autenticado
if (!isset($_SESSION['id_cliente']) || $_SESSION['tipo'] != 3) {
    echo "Debes iniciar sesión para ver el carrito.";
    exit();
}

$idcliente = $_SESSION['id_cliente'];

// Obtener carrito del cliente
$sql = "SELECT idcarrito FROM carrito WHERE clientes_idclientes = ? AND estado = 'activo' LIMIT 1";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param("i", $idcliente);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "No tienes productos en el carrito.";
    exit();
}

$idcarrito = $res->fetch_assoc()['idcarrito'];

// Obtener productos en carrito (usando precio_menudeo o precio_mayoreo)
$sql = "SELECT p.nombre, 
                CASE 
                    WHEN chp.cantidad >= 10 THEN p.precio_mayoreo
                    ELSE p.precio_menudeo
                END AS precio,
                chp.cantidad
        FROM carrito_has_productos chp
        JOIN productos p ON chp.productos_idproductos = p.idproductos
        WHERE chp.carrito_idcarrito = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param("i", $idcarrito);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Carrito de compras</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
  <h2>Tu Carrito</h2>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Precio unitario</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total = 0;
      while ($row = $result->fetch_assoc()) {
          $subtotal = $row['cantidad'] * $row['precio'];
          $total += $subtotal;
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
          echo "<td>" . $row['cantidad'] . "</td>";
          echo "<td>$" . number_format($row['precio'], 2) . "</td>";
          echo "<td>$" . number_format($subtotal, 2) . "</td>";
          echo "</tr>";
      }
      ?>
      <tr>
        <th colspan="3">Total</th>
        <th>$<?= number_format($total, 2) ?></th>
      </tr>
    </tbody>
  </table>

  <div class="d-flex justify-content-between mt-4">
    <a href="tienda.php" class="btn btn-secondary">← REGRESAR A TIENDA</a>
    <a href="abarrotes.php" class="btn btn-info">←AGREGAR MÁS PRODUCTOS</a>
    <a href="pago.php" class="btn btn-success">Pagar</a>
  </div>
</div>
</body>
</html>
