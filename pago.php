<?php
session_start();
include("conexion.php");

// Depuración para verificar la variable de sesión
//var_dump($_SESSION);  // Verifica si 'id_cliente' está disponible

// Validar sesión
if (!isset($_SESSION['id_cliente']) || empty($_SESSION['id_cliente']) || $_SESSION['tipo'] != 3) {
    echo "<script>
        alert('⚠️ Debes iniciar sesión para realizar el pago.');
        window.location.href = 'login.php';
    </script>";
    exit();
}

$idcliente = $_SESSION['id_cliente'];  // Asegúrate de usar 'id_cliente' correctamente

// Obtener carrito ACTIVO del cliente
$sql = "SELECT idcarrito FROM carrito WHERE clientes_idclientes = ? AND estado = 'activo' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idcliente);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<script>
        alert('🛒 No tienes productos en el carrito.');
        window.location.href = 'carrito.php';
    </script>";
    exit();
}

$idcarrito = $res->fetch_assoc()['idcarrito'];  // Obtener el idcarrito

// Obtener productos y calcular total
$sql_productos = "SELECT p.nombre, 
                          CASE 
                              WHEN chp.cantidad >= 10 THEN p.precio_mayoreo
                              ELSE p.precio_menudeo
                          END AS precio,
                          chp.cantidad
                  FROM carrito_has_productos chp
                  JOIN productos p ON chp.productos_idproductos = p.idproductos
                  WHERE chp.carrito_idcarrito = ?";

$stmt_productos = $conn->prepare($sql_productos);
$stmt_productos->bind_param("i", $idcarrito);
$stmt_productos->execute();
$result_productos = $stmt_productos->get_result();

$productos = [];
$total_compra = 0;

while ($row = $result_productos->fetch_assoc()) {
    $subtotal = $row['cantidad'] * $row['precio'];
    $row['subtotal'] = $subtotal;
    $productos[] = $row;
    $total_compra += $subtotal;
}

// Validar si total es 0
if ($total_compra == 0) {
    echo "<script>
        alert('⚠️ Tu carrito está vacío.');
        window.location.href = 'carrito.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Proceso de Pago</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
  <h2>Resumen de Compra</h2>
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
      <?php foreach ($productos as $prod): ?>
        <tr>
          <td><?= htmlspecialchars($prod['nombre']) ?></td>
          <td><?= $prod['cantidad'] ?></td>
          <td>$<?= number_format($prod['precio'], 2) ?></td>
          <td>$<?= number_format($prod['subtotal'], 2) ?></td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <th colspan="3">Total</th>
        <th><strong>$<?= number_format($total_compra, 2) ?></strong></th>
      </tr>
    </tbody>
  </table>

  <!-- Formulario de pago -->
  <h4>Selecciona tu método de pago</h4>
  <form action="procesar_pago.php" method="POST">
    <div class="mb-3">
      <label for="metodo_pago" class="form-label">Método de pago</label>
      <select name="metodo_pago" id="metodo_pago" class="form-control" required>
        <option value="tarjeta">Tarjeta de Crédito/Débito</option>
        <option value="transferencia">Transferencia Bancaria</option>
      </select>
    </div>

    <input type="hidden" name="idcarrito" value="<?= $idcarrito ?>">
    <input type="hidden" name="total" value="<?= $total_compra ?>">

    <button type="submit" class="btn btn-success">Confirmar Pago</button>
  </form>

  <a href="carrito.php" class="btn btn-secondary mt-3">← Regresar al carrito</a>
</div>
</body>
</html>
