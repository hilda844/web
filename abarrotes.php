<?php
session_start();
include("conexion.php");

// Obtener productos de la categoría 'Abarrotes y Despensa'
$sql = "SELECT * FROM productos WHERE categoria = 'Abarrotes y Despensa'";
$resultado = $conn->query($sql);

// Detectar tipo de usuario
$tipo = $_SESSION['tipo'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Abarrotes y Despensa</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script>
    function actualizarPrecio(id, precioMayoreo, precioMenudeo, piezasPorCaja) {
      const tipoVenta = document.getElementById('tipo_venta_' + id).value;
      const precioSpan = document.getElementById('precio_' + id);
      const cantidadInput = document.getElementById('cantidad_' + id);

      if (tipoVenta === 'mayoreo') {
        precioSpan.innerText = precioMayoreo.toFixed(2);
        cantidadInput.value = piezasPorCaja;
      } else {
        precioSpan.innerText = precioMenudeo.toFixed(2);
        cantidadInput.value = 1;
      }
    }
  </script>
</head>
<body>
<div class="container mt-4">
  <h2>🛒 Abarrotes y Despensa</h2>
  <div class="row">
    <?php while ($producto = $resultado->fetch_assoc()): ?>
      <?php
        $id = $producto['idproductos'];
        $precio_mayoreo = (float)$producto['precio_mayoreo'];
        $precio_menudeo = (float)$producto['precio_menudeo'];
        $unidad = htmlspecialchars($producto['unidad_medida']);
        $stock = (int)$producto['stock'];
        $piezas = (int)$producto['piezas_por_caja'];
      ?>
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($producto['nombre']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($producto['descripcion']) ?></p>
            <p><strong>Precio:</strong> $<span id="precio_<?= $id ?>"><?= number_format($precio_menudeo, 2) ?></span></p>
            <p><strong>Stock:</strong> <?= $stock ?></p>

            <?php if ($tipo == 3): ?>
              <form method="POST" action="agregar_carrito.php">
                <input type="hidden" name="idproducto" value="<?= $id ?>">
                <input type="hidden" name="unidad_medida" value="<?= $unidad ?>">

                <div class="mb-2">
                  <label class="form-label">Tipo de venta:</label>
                  <select name="tipo_venta" id="tipo_venta_<?= $id ?>" class="form-select"
                          onchange="actualizarPrecio(<?= $id ?>, <?= $precio_mayoreo ?>, <?= $precio_menudeo ?>, <?= $piezas ?>)">
                    <option value="menudeo">Menudeo</option>
                    <option value="mayoreo">Mayoreo</option>
                  </select>
                </div>

                <div class="mb-2">
                  <label class="form-label">Cantidad:</label>
                  <input type="number" name="cantidad" id="cantidad_<?= $id ?>" min="1" max="<?= $stock ?>" value="1" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-sm btn-success">Agregar al carrito</button>
              </form>

              <script>
                actualizarPrecio(<?= $id ?>, <?= $precio_mayoreo ?>, <?= $precio_menudeo ?>, <?= $piezas ?>);
              </script>
            <?php else: ?>
              <input type="number" value="1" class="form-control mb-2" disabled>
              <button class="btn btn-sm btn-secondary" onclick="alert('⚠️ Debes iniciar sesión para agregar productos al carrito.')" disabled>
                Agregar al carrito
              </button>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
  <a href="tienda.php" class="btn btn-secondary mt-4">← Volver a la tienda</a>
</div>
</body>
</html>
